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

// Fetch user information
$user_role = $_SESSION['role'];
$is_admin_or_cashier = ($user_role === 'Administrator' || $user_role === 'Cashier');

// Only admins and cashiers are allowed to fill prescriptions
if (!$is_admin_or_cashier) {
    $_SESSION['error'] = "Only admins and cashiers can fill prescriptions.";
    header("Location: ../prescriptions/prescriptions.php");
    exit;
}

// Fetch the prescription data
$prescription_id = $_GET['id'];
$prescription_stmt = $pdo->prepare("
    SELECT 
        p.prescription_id,
        p.prescription_date,
        p.status,
        c.name as customer_name,
        d.name as doctor_name
    FROM PRESCRIPTION p
    JOIN CUSTOMER c ON p.customer_id = c.customer_id
    JOIN DOCTOR d ON p.doctor_id = d.doctor_id
    WHERE p.prescription_id = ?
");
$prescription_stmt->execute([$prescription_id]);
$prescription = $prescription_stmt->fetch();

// Check if prescription exists
if (!$prescription) {
    $_SESSION['error'] = "Prescription not found.";
    header("Location: ../prescriptions/prescriptions.php");
    exit;
}

// Fetch prescription items (drugs, quantities, instructions)
$items_stmt = $pdo->prepare("SELECT * FROM PRESCRIPTION_ITEM WHERE prescription_id = ?");
$items_stmt->execute([$prescription_id]);
$items = $items_stmt->fetchAll();

// Handle form submission for filling the prescription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Update the prescription status to "Filled"
        $fill_prescription = $pdo->prepare("UPDATE PRESCRIPTION SET status = 'Filled' WHERE prescription_id = ?");
        $fill_prescription->execute([$prescription_id]);

        $_SESSION['success'] = "Prescription filled successfully";
        header("Location: ../prescriptions/view_prescription.php?id=" . $prescription_id);
        exit;

    } catch (Exception $e) {
        $_SESSION['error'] = "Error filling prescription: " . $e->getMessage();
    }
}
ob_end_flush();
?>

<div class="container mx-auto px-6 py-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-8">Fill Prescription</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Prescription Summary -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h3 class="text-xl font-semibold mb-4">Prescription Summary</h3>
        <div class="mb-4">
            <strong>Doctor Name:</strong> <?php echo htmlspecialchars($prescription['doctor_name']); ?>
        </div>
        <div class="mb-4">
            <strong>Created At:</strong> <?php echo date('M d, Y H:i', strtotime($prescription['prescription_date'])); ?>
        </div>
        <div class="mb-4">
            <strong>Patient:</strong> <?php echo htmlspecialchars($prescription['customer_name']); ?>
        </div>
        <div class="mb-4">
            <strong>Prescription Date:</strong> <?php echo date('M d, Y', strtotime($prescription['prescription_date'])); ?>
        </div>

        <h4 class="text-lg font-semibold mb-2">Prescription Items:</h4>
        <table class="w-full table-auto">
            <thead>
                <tr>
                    <th class="px-4 py-2 border">Medication</th>
                    <th class="px-4 py-2 border">Quantity</th>
                    <th class="px-4 py-2 border">Instructions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="px-4 py-2 border"><?php echo htmlspecialchars($item['drug_id']); ?> (Drug Name)</td> <!-- Replace with drug name if needed -->
                        <td class="px-4 py-2 border"><?php echo $item['quantity']; ?></td>
                        <td class="px-4 py-2 border"><?php echo htmlspecialchars($item['dosage_instructions']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Confirmation Form -->
    <form method="POST" class="space-y-6">
        <p class="text-lg mb-4">Are you sure you want to mark this prescription as filled?</p>

        <div class="flex justify-end space-x-4">
            <a href="../prescriptions/view_prescription.php?id=<?php echo $prescription_id; ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">Cancel</a>
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg">Fill Prescription</button>
        </div>
    </form>
</div>

<?php include_once '../includes/footer.php'; ?>
