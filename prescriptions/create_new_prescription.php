<?php
require_once '../includes/db_connect.php';
session_start();
include_once '../includes/header.php';

// Add these lines at the top after require_once
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Also, let's add session debugging
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in");
}

// Fetch all customers for dropdown
$customers = $pdo->query("SELECT customer_id, name FROM CUSTOMER ORDER BY name")->fetchAll();

// Fetch all doctors for dropdown
$doctors = $pdo->query("SELECT doctor_id, name FROM DOCTOR ORDER BY name")->fetchAll();

// Fetch all drugs for dropdown
$drugs = $pdo->query("SELECT drug_id, name, dosage_form FROM DRUG ORDER BY name")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Insert prescription
        $create_prescription = $pdo->prepare("
            INSERT INTO PRESCRIPTION 
            (customer_id, doctor_id, prescription_date, status, user_id) 
            VALUES (?, ?, ?, 'Pending', ?)
        ");
        
        $create_prescription->execute([
            $_POST['customer_id'],
            $_POST['doctor_id'],
            $_POST['prescription_date'],
            $_SESSION['user_id'] // Assuming user_id is stored in session
        ]);

        $prescription_id = $pdo->lastInsertId();

        // Insert prescription items
        $insert_item = $pdo->prepare("
            INSERT INTO PRESCRIPTION_ITEM 
            (prescription_id, drug_id, quantity, dosage_instructions) 
            VALUES (?, ?, ?, ?)
        ");

        foreach ($_POST['items'] as $item) {
            if (!empty($item['drug_id']) && !empty($item['quantity'])) {
                $insert_item->execute([
                    $prescription_id,
                    $item['drug_id'],
                    $item['quantity'],
                    $item['dosage_instructions']
                ]);
            }
        }

        $pdo->commit();
        $_SESSION['success'] = "Prescription created successfully";
        header("Location: view.php?id=" . $prescription_id);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = $e->getMessage();
    }
}
?>

<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Create New Prescription</h2>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-semibold mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Patient</label>
                    <select name="customer_id" required class="w-full rounded-lg border-gray-300">
                        <option value="">Select Patient</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['customer_id']; ?>">
                                <?php echo htmlspecialchars($customer['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Doctor</label>
                    <select name="doctor_id" required class="w-full rounded-lg border-gray-300">
                        <option value="">Select Doctor</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?php echo $doctor['doctor_id']; ?>">
                                <?php echo htmlspecialchars($doctor['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" name="prescription_date" 
                           value="<?php echo date('Y-m-d'); ?>"
                           required class="w-full rounded-lg border-gray-300">
                </div>
            </div>
        </div>

        <!-- Prescription Items -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-semibold mb-4">Prescription Items</h3>
            <div id="items-container">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 item-row">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Medication</label>
                        <select name="items[0][drug_id]" required class="w-full rounded-lg border-gray-300">
                            <option value="">Select Medication</option>
                            <?php foreach ($drugs as $drug): ?>
                                <option value="<?php echo $drug['drug_id']; ?>">
                                    <?php echo htmlspecialchars($drug['name'] . ' (' . $drug['dosage_form'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <input type="number" name="items[0][quantity]" 
                               required min="1" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                        <input type="text" name="items[0][dosage_instructions]" 
                               required class="w-full rounded-lg border-gray-300"
                               placeholder="e.g., Take one tablet twice daily after meals">
                    </div>
                </div>
            </div>
            <button type="button" id="add-item" 
                    class="mt-4 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Add Another Medication
            </button>
        </div>
        <div class="flex justify-end space-x-4">
            <a href="<?php echo $base_url; ?>/prescriptions/prescriptions.php" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                Cancel
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                Create Prescription
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const index = container.children.length;
    const template = `
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 item-row">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Medication</label>
                <select name="items[${index}][drug_id]" required class="w-full rounded-lg border-gray-300">
                    <option value="">Select Medication</option>
                    <?php foreach ($drugs as $drug): ?>
                        <option value="<?php echo $drug['drug_id']; ?>">
                            <?php echo htmlspecialchars($drug['name'] . ' (' . $drug['dosage_form'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                <input type="number" name="items[${index}][quantity]" required min="1" 
                       class="w-full rounded-lg border-gray-300">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                <input type="text" name="items[${index}][dosage_instructions]" required 
                       class="w-full rounded-lg border-gray-300"
                       placeholder="e.g., Take one tablet twice daily after meals">
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', template);
});

document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate basic information
    const customer = document.querySelector('[name="customer_id"]').value;
    const doctor = document.querySelector('[name="doctor_id"]').value;
    const date = document.querySelector('[name="prescription_date"]').value;

    if (!customer || !doctor || !date) {
        alert('Please fill in all basic information fields');
        return;
    }

    // Validate at least one medication
    const medications = document.querySelectorAll('[name^="items"][name$="[drug_id]"]');
    const quantities = document.querySelectorAll('[name^="items"][name$="[quantity]"]');
    const instructions = document.querySelectorAll('[name^="items"][name$="[dosage_instructions]"]');

    let hasValidItem = false;
    for (let i = 0; i < medications.length; i++) {
        if (medications[i].value && quantities[i].value && instructions[i].value) {
            hasValidItem = true;
            break;
        }
    }

    if (!hasValidItem) {
        alert('Please add at least one medication with quantity and instructions');
        return;
    }

    // If validation passes, submit the form
    this.submit();
});
</script>

<?php include_once '../includes/footer.php'; ?> 