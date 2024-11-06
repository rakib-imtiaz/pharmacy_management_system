<?php
require_once '../includes/db_connect.php';
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $dosage_form = $_POST['dosage_form'];
    $description = $_POST['description'];
    $supplier_id = $_POST['supplier_id'];
    $unit_price = $_POST['unit_price'];
    $quantity = $_POST['quantity'];
    $expiry_date = $_POST['expiry_date'];

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Insert into DRUG table
        $drug_stmt = $pdo->prepare("
            INSERT INTO DRUG (name, category_id, dosage_form, description)
            VALUES (?, ?, ?, ?)
        ");
        $drug_stmt->execute([$name, $category_id, $dosage_form, $description]);
        $drug_id = $pdo->lastInsertId();

        // Insert into STOCK_ITEM table
        $stock_stmt = $pdo->prepare("
            INSERT INTO STOCK_ITEM (drug_id, quantity, expiry_date, unit_price, supplier_id, user_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stock_stmt->execute([
            $drug_id,
            $quantity,
            $expiry_date,
            $unit_price,
            $supplier_id,
            $_SESSION['user_id']
        ]);

        // Log the action
        $audit_stmt = $pdo->prepare("
            INSERT INTO AUDIT_LOG (user_id, timestamp, action, table_affected, record_id)
            VALUES (?, NOW(), 'INSERT', 'DRUG', ?)
        ");
        $audit_stmt->execute([$_SESSION['user_id'], $drug_id]);

        $pdo->commit();
        $_SESSION['success'] = "Drug added successfully";
        header("Location: " . $base_url . "inventory/inventory.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error adding drug: " . $e->getMessage();
    }
}

require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Add New Drug</h1>
            <a href="<?php echo $base_url; ?>inventory/inventory.php" class="text-blue-500 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to Inventory
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="" method="POST">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name *</label>
                    <input type="text" name="name" id="name" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Category *</label>
                    <select name="category_id" id="category_id" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select Category</option>
                        <?php
                        $categories = $pdo->query("SELECT * FROM DRUG_CATEGORY ORDER BY name");
                        while ($category = $categories->fetch()) {
                            echo "<option value='{$category['category_id']}'>{$category['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="dosage_form" class="block text-gray-700 text-sm font-bold mb-2">Dosage Form *</label>
                    <select name="dosage_form" id="dosage_form" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select Dosage Form</option>
                        <option value="Tablet">Tablet</option>
                        <option value="Capsule">Capsule</option>
                        <option value="Syrup">Syrup</option>
                        <option value="Injection">Injection</option>
                        <option value="Cream">Cream</option>
                        <option value="Ointment">Ointment</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description *</label>
                    <textarea name="description" id="description" required
                              class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                              rows="3"></textarea>
                </div>

                <div class="mb-4">
                    <label for="supplier_id" class="block text-gray-700 text-sm font-bold mb-2">Supplier *</label>
                    <select name="supplier_id" id="supplier_id" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select Supplier</option>
                        <?php
                        $suppliers = $pdo->query("SELECT * FROM SUPPLIER ORDER BY name");
                        while ($supplier = $suppliers->fetch()) {
                            echo "<option value='{$supplier['supplier_id']}'>{$supplier['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="unit_price" class="block text-gray-700 text-sm font-bold mb-2">Unit Price (₱) *</label>
                    <input type="number" name="unit_price" id="unit_price" step="0.01" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Initial Quantity *</label>
                    <input type="number" name="quantity" id="quantity" required min="0"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-6">
                    <label for="expiry_date" class="block text-gray-700 text-sm font-bold mb-2">Expiry Date *</label>
                    <input type="date" name="expiry_date" id="expiry_date" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Add Drug
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 