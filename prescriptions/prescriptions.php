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

// Determine the query based on the user's role
$query = "";
if ($is_admin || $is_cashier) {
    // Admin and Cashier can view all prescriptions
    $query = "
        SELECT 
            p.prescription_id,
            p.prescription_date,
            p.status,
            c.name as customer_name,
            d.name as doctor_name
        FROM PRESCRIPTION p
        JOIN CUSTOMER c ON p.customer_id = c.customer_id
        JOIN DOCTOR d ON p.doctor_id = d.doctor_id
        ORDER BY 
            p.status DESC,  -- First, prioritize pending prescriptions
            p.prescription_date DESC  -- Then, order by date for both pending and non-pending prescriptions
    ";
} elseif ($is_doctor) {
    // Doctors can only view their own prescriptions
    $query = "
        SELECT 
            p.prescription_id,
            p.prescription_date,
            p.status,
            c.name as customer_name,
            d.name as doctor_name
        FROM PRESCRIPTION p
        JOIN CUSTOMER c ON p.customer_id = c.customer_id
        JOIN DOCTOR d ON p.doctor_id = d.doctor_id
        JOIN USER u ON d.user_id = u.user_id
        WHERE d.user_id = ?  -- Only prescriptions assigned to the logged-in doctor
        ORDER BY 
            p.status DESC,  -- First, prioritize pending prescriptions
            p.prescription_date DESC  -- Then, order by date for both pending and non-pending prescriptions
    ";
}


$prescriptions = [];
try {
    // Prepare and execute the query based on the role
    if ($is_doctor) {
        // For doctors, we pass the doctor's user_id to the query
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id]);
    } else {
        // Admin and Cashier do not need any parameters
        $stmt = $pdo->query($query);
    }
    $prescriptions = $stmt->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error'] = "Error fetching prescriptions: " . $e->getMessage();
}

?>

<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Prescription Management</h2>
        <?php if ($is_doctor): ?>
            <a href="<?php echo $base_url; ?>prescriptions/create_new_prescription.php" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-plus mr-2"></i>New Prescription
            </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($prescriptions as $prescription): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo $prescription['prescription_id']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo date('M d, Y', strtotime($prescription['prescription_date'])); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($prescription['customer_name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($prescription['doctor_name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php echo $prescription['status'] === 'Filled' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                <?php echo $prescription['status']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="<?php echo $base_url; ?>prescriptions/view_prescription.php?id=<?php echo $prescription['prescription_id']; ?>" 
                               class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>

                            <?php if ($is_doctor && $prescription['doctor_name'] === $_SESSION['username']): ?>
                                <!-- Edit option only for the doctor who created it -->
                                <a href="<?php echo $base_url; ?>prescriptions/edit_prescription.php?id=<?php echo $prescription['prescription_id']; ?>" 
                                   class="text-green-600 hover:text-green-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                            <?php endif; ?>

                            <?php if ($is_admin || $is_cashier): ?>
                                <!-- Fill option only for admin and cashier -->
                                <?php if ($prescription['status'] !== 'Filled'): ?>
                                    <a href="<?php echo $base_url; ?>prescriptions/fill_prescription.php?id=<?php echo $prescription['prescription_id']; ?>" 
                                       class="text-purple-600 hover:text-purple-900 mr-3">
                                        <i class="fas fa-prescription-bottle-alt"></i>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if ($is_admin || ($is_doctor && $prescription['doctor_name'] === $_SESSION['username'])): ?>
                                <!-- Delete option for admin and the doctor who created it -->
                                <a href="<?php echo $base_url; ?>prescriptions/delete_prescription.php?id=<?php echo $prescription['prescription_id']; ?>" 
                                   class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
