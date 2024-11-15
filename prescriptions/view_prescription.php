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
$is_admin = ($user_role === 'Administrator');
$is_cashier = ($user_role === 'Cashier');
$is_doctor = ($user_role === 'Doctor');

// Fetch the prescription details
$prescription_id = $_GET['id'];
$prescription_stmt = $pdo->prepare("
    SELECT p.prescription_id, p.prescription_date, p.status, c.name AS customer_name, d.name AS doctor_name, d.doctor_id
    FROM PRESCRIPTION p
    JOIN CUSTOMER c ON p.customer_id = c.customer_id
    JOIN DOCTOR d ON p.doctor_id = d.doctor_id
    WHERE p.prescription_id = ?
");
$prescription_stmt->execute([$prescription_id]);
$prescription = $prescription_stmt->fetch();

// Check if the prescription exists
if (!$prescription) {
    $_SESSION['error'] = "Prescription not found.";
    header("Location: ../prescriptions/prescriptions.php");
    exit;
}

// Fetch prescription items
$items_stmt = $pdo->prepare("SELECT pi.*, d.name AS drug_name, d.dosage_form FROM PRESCRIPTION_ITEM pi JOIN DRUG d ON pi.drug_id = d.drug_id WHERE pi.prescription_id = ?");
$items_stmt->execute([$prescription_id]);
$items = $items_stmt->fetchAll();
?>

<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Prescription Details</h2>
        
        <div class="flex space-x-4">
            <?php if ($is_doctor && $prescription['doctor_id'] == $user_id): ?>
                <!-- Edit option only for the doctor who created the prescription -->
                <a href="<?php echo $base_url; ?>prescriptions/edit_prescription.php?id=<?php echo $prescription_id; ?>" 
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                   <i class="fas fa-edit"></i> Edit Prescription
                </a>
            <?php endif; ?>

            <?php if ($is_admin || $is_cashier): ?>
                <!-- Fill option only for admin and cashier -->
                <?php if ($prescription['status'] !== 'Filled'): ?>
                    <a href="<?php echo $base_url; ?>prescriptions/fill_prescription.php?id=<?php echo $prescription_id; ?>" 
                       class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">
                       <i class="fas fa-prescription-bottle-alt"></i> Fill Prescription
                    </a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($is_admin || ($is_doctor && $prescription['doctor_id'] == $user_id)): ?>
                <!-- Delete option for admin and the doctor who created the prescription -->
                <a href="<?php echo $base_url; ?>prescriptions/delete_prescription.php?id=<?php echo $prescription_id; ?>" 
                   class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                   <i class="fas fa-trash-alt"></i> Delete Prescription
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Prescription Details -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h3 class="text-xl font-semibold mb-4">Basic Information</h3>
        <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($prescription['prescription_date'])); ?></p>
        <p><strong>Status:</strong> <?php echo $prescription['status']; ?></p>
        <p><strong>Patient:</strong> <?php echo htmlspecialchars($prescription['customer_name']); ?></p>
        <p><strong>Doctor:</strong> <?php echo htmlspecialchars($prescription['doctor_name']); ?></p>
    </div>

    <!-- Prescription Items -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-semibold mb-4">Prescription Items</h3>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medication</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosage Form</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($items as $item): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($item['drug_name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($item['dosage_form']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($item['dosage_instructions']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
