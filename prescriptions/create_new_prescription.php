<?php
require_once '../includes/db_connect.php';
session_start();
include_once '../includes/header.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['error'] = "Unauthorized access. Please log in as a doctor.";
    header("Location: ../login.php");
    exit;
}

// Fetch user information from the session
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$is_doctor = ($user_role === 'Doctor');

// Only doctors can create prescriptions
if (!$is_doctor) {
    $_SESSION['error'] = "Only doctors can create prescriptions.";
    header("Location: ../prescriptions/prescriptions.php");
    exit;
}

// Fetch the doctor associated with the logged-in user
$doctor_stmt = $pdo->prepare("SELECT doctor_id, name FROM DOCTOR WHERE user_id = ?");
$doctor_stmt->execute([$user_id]);
$doctor = $doctor_stmt->fetch();

// Check if the doctor profile exists
if (!$doctor) {
    $_SESSION['error'] = "Doctor profile not found. Please contact the administrator.";
    header("Location: ../prescriptions/prescriptions.php");
    exit;
}

// Fetch dropdown data for customers and drugs
$customers = $pdo->query("SELECT customer_id, name FROM CUSTOMER ORDER BY name")->fetchAll();
$drugs = $pdo->query("SELECT drug_id, name, dosage_form FROM DRUG ORDER BY name")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure that the doctor_id in the form is the same as the logged-in doctor’s ID
    if ($_POST['doctor_id'] != $doctor['doctor_id']) {
        $_SESSION['error'] = "You can only create prescriptions for yourself.";
        header("Location: create_new_prescription.php");
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Insert prescription data
        $create_prescription = $pdo->prepare("
            INSERT INTO PRESCRIPTION 
            (customer_id, doctor_id, prescription_date, status) 
            VALUES (?, ?, ?, 'Pending')
        ");
        
        $create_prescription->execute([
            $_POST['customer_id'],
            $doctor['doctor_id'], // Use the logged-in doctor’s ID
            $_POST['prescription_date']
        ]);

        $prescription_id = $pdo->lastInsertId();

        // Insert each prescription item
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

        // Commit transaction
        $pdo->commit();
        $_SESSION['success'] = "Prescription created successfully";
        
        // Redirect to the prescription view page
        header("Location: ../prescriptions/view_prescription.php?id=" . $prescription_id);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error creating prescription: " . $e->getMessage();
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
                    <input type="hidden" name="doctor_id" value="<?php echo $doctor['doctor_id']; ?>">
                    <input type="text" class="w-full rounded-lg border-gray-300 bg-gray-100" value="<?php echo htmlspecialchars($doctor['name']); ?>" readonly>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" name="prescription_date" value="<?php echo date('Y-m-d'); ?>" required class="w-full rounded-lg border-gray-300">
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
                        <input type="number" name="items[0][quantity]" required min="1" class="w-full rounded-lg border-gray-300">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                        <input type="text" name="items[0][dosage_instructions]" required class="w-full rounded-lg border-gray-300"
                               placeholder="e.g., Take one tablet twice daily after meals">
                    </div>
                </div>
            </div>
            <button type="button" id="add-item" class="mt-4 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
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
<?php include_once '../includes/footer.php'; ?>
