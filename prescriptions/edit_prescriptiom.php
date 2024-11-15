<?php
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

// Fetch user information
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$is_doctor = ($user_role === 'Doctor');

// Only doctors can edit prescriptions
if (!$is_doctor) {
    $_SESSION['error'] = "Only doctors can edit prescriptions.";
    header("Location: ../prescriptions/prescriptions.php");
    exit;
}

// Fetch the prescription and validate access
$prescription_id = $_GET['id'];
$prescription_stmt = $pdo->prepare("SELECT * FROM PRESCRIPTION WHERE prescription_id = ?");
$prescription_stmt->execute([$prescription_id]);
$prescription = $prescription_stmt->fetch();

// Check if the prescription exists and belongs to the logged-in doctor
if (!$prescription || $prescription['doctor_id'] != $user_id) {
    $_SESSION['error'] = "You are not authorized to edit this prescription.";
    header("Location: ../prescriptions/prescriptions.php");
    exit;
}

// Fetch dropdown data for customers and drugs
$customers = $pdo->query("SELECT customer_id, name FROM CUSTOMER ORDER BY name")->fetchAll();
$drugs = $pdo->query("SELECT drug_id, name, dosage_form FROM DRUG ORDER BY name")->fetchAll();

// Handle form submission for editing prescription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Update prescription data
        $update_prescription = $pdo->prepare("
            UPDATE PRESCRIPTION SET customer_id = ?, prescription_date = ?, status = ?
            WHERE prescription_id = ? AND doctor_id = ?
        ");
        $update_prescription->execute([
            $_POST['customer_id'],
            $_POST['prescription_date'],
            $_POST['status'],
            $prescription_id,
            $user_id
        ]);

        // Clear existing items and insert updated items
        $pdo->prepare("DELETE FROM PRESCRIPTION_ITEM WHERE prescription_id = ?")->execute([$prescription_id]);

        $insert_item = $pdo->prepare("
            INSERT INTO PRESCRIPTION_ITEM (prescription_id, drug_id, quantity, dosage_instructions)
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
        $_SESSION['success'] = "Prescription updated successfully";
        header("Location: ../prescriptions/view_prescription.php?id=" . $prescription_id);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error updating prescription: " . $e->getMessage();
    }
}
?>

<div class="container mx-auto px-6 py-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-8">Edit Prescription</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
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
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['customer_id']; ?>" <?php echo ($customer['customer_id'] == $prescription['customer_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($customer['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" name="prescription_date" value="<?php echo $prescription['prescription_date']; ?>" required class="w-full rounded-lg border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300">
                        <option value="Pending" <?php echo ($prescription['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="Filled" <?php echo ($prescription['status'] === 'Filled') ? 'selected' : ''; ?>>Filled</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Prescription Items -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-semibold mb-4">Prescription Items</h3>
            <div id="items-container">
                <?php
                $items_stmt = $pdo->prepare("SELECT * FROM PRESCRIPTION_ITEM WHERE prescription_id = ?");
                $items_stmt->execute([$prescription_id]);
                $items = $items_stmt->fetchAll();
                foreach ($items as $index => $item): ?>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 item-row">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Medication</label>
                            <select name="items[<?php echo $index; ?>][drug_id]" required class="w-full rounded-lg border-gray-300">
                                <?php foreach ($drugs as $drug): ?>
                                    <option value="<?php echo $drug['drug_id']; ?>" <?php echo ($drug['drug_id'] == $item['drug_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($drug['name'] . ' (' . $drug['dosage_form'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <input type="number" name="items[<?php echo $index; ?>][quantity]" value="<?php echo $item['quantity']; ?>" required min="1" class="w-full rounded-lg border-gray-300">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                            <input type="text" name="items[<?php echo $index; ?>][dosage_instructions]" value="<?php echo $item['dosage_instructions']; ?>" required class="w-full rounded-lg border-gray-300">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="add-item" class="mt-4 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Add Another Medication</button>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="../prescriptions/view_prescription.php?id=<?php echo $prescription_id; ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">Cancel</a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">Update Prescription</button>
        </div>
    </form>
</div>

<?php include_once '../includes/footer.php'; ?>
