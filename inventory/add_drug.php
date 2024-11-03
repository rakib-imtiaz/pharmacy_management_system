<?php
require_once '../includes/db_connect.php';
session_start();

// Check if it's an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        $required_fields = ['name', 'category_id', 'dosage_form', 'description', 'supplier_id', 'unit_price', 'quantity', 'expiry_date'];
        $errors = [];
        
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        if (empty($errors)) {
            // Begin transaction
            $pdo->beginTransaction();

            // Insert into DRUG table
            $drug_stmt = $pdo->prepare("
                INSERT INTO DRUG (name, category_id, dosage_form, description)
                VALUES (?, ?, ?, ?)
            ");

            $drug_stmt->execute([
                $_POST['name'],
                $_POST['category_id'],
                $_POST['dosage_form'],
                $_POST['description']
            ]);

            $drug_id = $pdo->lastInsertId();

            // Insert into STOCK_ITEM table
            $stock_stmt = $pdo->prepare("
                INSERT INTO STOCK_ITEM (drug_id, quantity, expiry_date, unit_price, supplier_id, user_id)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stock_stmt->execute([
                $drug_id,
                $_POST['quantity'],
                $_POST['expiry_date'],
                $_POST['unit_price'],
                $_POST['supplier_id'],
                $_SESSION['user_id']
            ]);

            // Log the action
            $audit_stmt = $pdo->prepare("
                INSERT INTO AUDIT_LOG (user_id, timestamp, action, table_affected, record_id)
                VALUES (?, NOW(), 'INSERT', 'DRUG', ?)
            ");

            $audit_stmt->execute([$_SESSION['user_id'], $drug_id]);

            $pdo->commit();
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => $_POST['name'] . ' has been added successfully'
                ]);
                exit;
            } else {
                $_SESSION['success_message'] = $_POST['name'] . ' has been added successfully';
                header('Location: inventory.php');
                exit;
            }
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $errors[] = "Database error: " . $e->getMessage();
        
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
}

// Only include header and show form for non-AJAX requests
if (!$isAjax) {
    include_once '../includes/header.php';
    // ... rest of your HTML form code ...
}
?>

<div class="relative min-h-screen">
    <div class="absolute inset-0 z-0 pointer-events-none">
        <img src="../assets/images/image2.png" class="w-full h-full object-cover opacity-15" alt="Background">
    </div>

    <div class="container mx-auto px-6 py-8 relative z-10">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 animate__animated animate__fadeIn">
                Add New Drug
                <span class="block text-lg font-normal text-gray-600 mt-2">Enter new drug details</span>
            </h2>
            <button onclick="window.location.href='inventory.php'" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Back to Inventory
            </button>
        </div>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 animate__animated animate__fadeIn" role="alert">
                <strong class="font-bold">Please fix the following errors:</strong>
                <ul class="mt-2 list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Add Drug Form -->
        <div class="bg-white rounded-xl shadow-lg p-8 animate__animated animate__fadeInUp">
            <form method="POST" class="space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Drug Name *</label>
                        <input type="text" name="name" required
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., Paracetamol 500mg"
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        <p class="mt-1 text-sm text-gray-500">Enter the complete name including strength if applicable</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select name="category_id" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Category</option>
                            <?php
                            $categories = $pdo->query("SELECT * FROM DRUG_CATEGORY ORDER BY name");
                            while ($category = $categories->fetch()) {
                                $selected = (isset($_POST['category_id']) && $_POST['category_id'] == $category['category_id']) ? 'selected' : '';
                                echo "<option value='{$category['category_id']}' {$selected}>{$category['name']}</option>";
                            }
                            ?>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Choose the appropriate drug category</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dosage Form *</label>
                        <select name="dosage_form" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Dosage Form</option>
                            <option value="Tablet">Tablet</option>
                            <option value="Capsule">Capsule</option>
                            <option value="Syrup">Syrup</option>
                            <option value="Injection">Injection</option>
                            <option value="Cream">Cream</option>
                            <option value="Ointment">Ointment</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Select how the drug is administered</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea name="description" required
                                  class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="e.g., Pain reliever and fever reducer"
                                  rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                        <p class="mt-1 text-sm text-gray-500">Brief description of the drug's purpose and usage</p>
                    </div>
                </div>

                <!-- Stock Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Supplier *</label>
                        <select name="supplier_id" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Supplier</option>
                            <?php
                            $suppliers = $pdo->query("SELECT * FROM SUPPLIER ORDER BY name");
                            while ($supplier = $suppliers->fetch()) {
                                $selected = (isset($_POST['supplier_id']) && $_POST['supplier_id'] == $supplier['supplier_id']) ? 'selected' : '';
                                echo "<option value='{$supplier['supplier_id']}' {$selected}>{$supplier['name']}</option>";
                            }
                            ?>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Select the drug supplier</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price (₱) *</label>
                        <input type="number" name="unit_price" step="0.01" required
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., 25.50"
                               value="<?php echo isset($_POST['unit_price']) ? htmlspecialchars($_POST['unit_price']) : ''; ?>">
                        <p class="mt-1 text-sm text-gray-500">Enter the price per unit in Philippine Peso</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Initial Quantity *</label>
                        <input type="number" name="quantity" required min="0"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., 100"
                               value="<?php echo isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : ''; ?>">
                        <p class="mt-1 text-sm text-gray-500">Enter the initial stock quantity</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date *</label>
                        <input type="date" name="expiry_date" required
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               min="<?php echo date('Y-m-d'); ?>"
                               value="<?php echo isset($_POST['expiry_date']) ? htmlspecialchars($_POST['expiry_date']) : ''; ?>">
                        <p class="mt-1 text-sm text-gray-500">Select the drug's expiration date</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="window.location.href='inventory.php'"
                            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-300">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300">
                        Add Drug
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const requiredFields = ['name', 'category_id', 'dosage_form', 'description', 'supplier_id', 'unit_price', 'quantity', 'expiry_date'];
    const errors = [];

    requiredFields.forEach(field => {
        const input = this.elements[field];
        if (!input.value.trim()) {
            errors.push(`${field.replace('_', ' ')} is required`);
            input.classList.add('border-red-500');
        } else {
            input.classList.remove('border-red-500');
        }
    });

    if (errors.length > 0) {
        Swal.fire({
            title: 'Error!',
            html: errors.join('<br>'),
            icon: 'error',
            confirmButtonText: 'Ok'
        });
        return;
    }

    // Show loading state
    Swal.fire({
        title: 'Adding Drug...',
        text: 'Please wait while we process your request',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Submit form data using fetch
    const formData = new FormData(this);
    fetch('add_drug.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'inventory.php';
            });
        } else {
            throw new Error(data.message || 'Failed to add drug');
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error!',
            text: error.message,
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    });
});

// Prevent form resubmission on page refresh
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
</script>

<?php include_once 'includes/footer.php'; ?> 