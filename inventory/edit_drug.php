<?php
ob_start();

require_once '../includes/db_connect.php';
session_start();
include_once '../includes/header.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['error'] = "Unauthorized access. Please log in.";
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$is_admin = ($role === 'Administrator');
$is_supplier = ($role === 'Supplier');

// Get drug details
if (isset($_GET['id'])) {
    $drug_id = $_GET['id'];
    try {
        // Get drug and stock information
        $stmt = $pdo->prepare("
            SELECT d.*, s.quantity, s.expiry_date, s.unit_price, s.supplier_id, s.stock_item_id
            FROM DRUG d
            LEFT JOIN STOCK_ITEM s ON d.drug_id = s.drug_id
            WHERE d.drug_id = ?
        ");
        $stmt->execute([$drug_id]);
        $drug = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$drug) {
            $_SESSION['error'] = "Drug not found.";
            header('Location: inventory.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error fetching drug details: " . $e->getMessage();
        header('Location: inventory.php');
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        $errors = [];

        if ($is_admin) {
            // Admin can update all fields including quantity
            $drug_stmt = $pdo->prepare("
                UPDATE DRUG 
                SET name = ?, category_id = ?, dosage_form = ?, description = ? 
                WHERE drug_id = ?
            ");
            $drug_stmt->execute([
                $_POST['name'],
                $_POST['category_id'],
                $_POST['dosage_form'],
                $_POST['description'],
                $drug_id
            ]);

            // Update stock quantity and other fields
            $stock_stmt = $pdo->prepare("
                UPDATE STOCK_ITEM 
                SET quantity = ?, expiry_date = ?, unit_price = ? 
                WHERE stock_item_id = ?
            ");
            $stock_stmt->execute([
                $_POST['quantity'],  // New quantity
                $_POST['expiry_date'], // New expiry date
                $_POST['unit_price'], // New unit price
                $_POST['stock_item_id'] // Stock item ID to identify the stock row
            ]);
        } elseif ($is_supplier) {
            // Supplier can only update quantity and expiry date
            $stock_stmt = $pdo->prepare("
                UPDATE STOCK_ITEM 
                SET quantity = ?, expiry_date = ? 
                WHERE stock_item_id = ? AND supplier_id = ?
            ");
            $stock_stmt->execute([
                $_POST['quantity'],
                $_POST['expiry_date'],
                $_POST['stock_item_id'],
                $user_id // Ensure supplier can only update their own stock
            ]);
        }

        // Log the update
        $audit_stmt = $pdo->prepare("
            INSERT INTO AUDIT_LOG (user_id, timestamp, action, table_affected, record_id)
            VALUES (?, NOW(), 'UPDATE', 'DRUG', ?)
        ");
        $audit_stmt->execute([$user_id, $drug_id]);

        $pdo->commit();

        $_SESSION['success_message'] = "Drug updated successfully";
        header('Location: inventory.php');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $errors[] = "Database error: " . $e->getMessage();
    }
}

include_once '../includes/header.php';
?>

<div class="relative min-h-screen">
    <div class="absolute inset-0 z-0 pointer-events-none">
        <img src="../assets/images/image2.png" class="w-full h-full object-cover opacity-15" alt="Background">
    </div>

    <div class="container mx-auto px-6 py-8 relative z-10">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 animate__animated animate__fadeIn">
                Edit Drug
                <span class="block text-lg font-normal text-gray-600 mt-2">Update drug information</span>
            </h2>
            <button onclick="window.location.href='inventory.php'" 
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Inventory
            </button>
        </div>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Please fix the following errors:</strong>
                <ul class="mt-2 list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Edit Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" class="space-y-6">
                <input type="hidden" name="stock_item_id" value="<?php echo htmlspecialchars($drug['stock_item_id']); ?>">
                
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Drug Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Drug Name *</label>
                        <input type="text" name="name" <?php echo $is_admin ? '' : 'readonly'; ?>
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="<?php echo htmlspecialchars($drug['name']); ?>">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select name="category_id" <?php echo $is_admin ? '' : 'disabled'; ?>
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php
                            $categories = $pdo->query("SELECT * FROM DRUG_CATEGORY ORDER BY name");
                            while ($category = $categories->fetch()) {
                                $selected = ($drug['category_id'] == $category['category_id']) ? 'selected' : '';
                                echo "<option value='{$category['category_id']}' {$selected}>{$category['name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Dosage Form -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dosage Form *</label>
                        <select name="dosage_form" <?php echo $is_admin ? '' : 'disabled'; ?>
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php
                            $dosage_forms = ['Tablet', 'Capsule', 'Syrup', 'Injection', 'Cream', 'Ointment'];
                            foreach ($dosage_forms as $form) {
                                $selected = ($drug['dosage_form'] == $form) ? 'selected' : '';
                                echo "<option value='{$form}' {$selected}>{$form}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea name="description" <?php echo $is_admin ? '' : 'readonly'; ?>
                                  class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  rows="3"><?php echo htmlspecialchars($drug['description']); ?></textarea>
                    </div>
                </div>

                <!-- Stock Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Supplier (Read-only for both Admin and Supplier) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Supplier *</label>
                        <select name="supplier_id" disabled
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php
                            $suppliers = $pdo->query("SELECT * FROM SUPPLIER ORDER BY name");
                            while ($supplier = $suppliers->fetch()) {
                                $selected = ($drug['supplier_id'] == $supplier['supplier_id']) ? 'selected' : '';
                                echo "<option value='{$supplier['supplier_id']}' {$selected}>{$supplier['name']}</option>";
                            }
                            ?>
                        </select>
                        <input type="hidden" name="supplier_id" value="<?php echo htmlspecialchars($drug['supplier_id']); ?>">
                    </div>

                    <!-- Unit Price (Read-only for Supplier, editable for Admin) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price (₱) *</label>
                        <input type="number" name="unit_price" step="0.01"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="<?php echo htmlspecialchars($drug['unit_price']); ?>" <?php echo $is_supplier ? 'readonly' : ''; ?>>
                    </div>

                    <!-- Quantity (Editable by both Admin and Supplier) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                        <input type="number" name="quantity" required min="0"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="<?php echo htmlspecialchars($drug['quantity']); ?>" <?php echo $is_supplier ? '' : 'readonly'; ?>>
                    </div>

                    <!-- Expiry Date (Editable by both Admin and Supplier) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date *</label>
                        <input type="date" name="expiry_date" required
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               value="<?php echo htmlspecialchars($drug['expiry_date']); ?>" <?php echo $is_supplier ? '' : 'readonly'; ?>>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="window.location.href='inventory.php'"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-300">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300">
                        Update Drug
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('input', function() {
        if (this.value < 0) {
            this.value = 0;
        }
    });
});
</script>

<?php include_once '../includes/footer.php'; ?>
