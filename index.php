<?php
require_once 'includes/db_connect.php';
session_start();

// Verify login status
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include_once 'includes/header.php';

// Fetch user-specific data
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];
$is_admin = ($role === 'Administrator');

// Get statistics based on role
$stats = [];
if ($is_admin) {
    $stats = [
        ['icon' => 'fas fa-pills', 'title' => 'Total Drugs', 'query' => "SELECT COUNT(*) FROM DRUG", 'color' => 'blue'],
        ['icon' => 'fas fa-user-md', 'title' => 'Doctors', 'query' => "SELECT COUNT(*) FROM DOCTOR", 'color' => 'indigo'],
        ['icon' => 'fas fa-users', 'title' => 'Customers', 'query' => "SELECT COUNT(*) FROM CUSTOMER", 'color' => 'green'],
        ['icon' => 'fas fa-file-prescription', 'title' => 'Prescriptions', 'query' => "SELECT COUNT(*) FROM PRESCRIPTION", 'color' => 'purple'],
        ['icon' => 'fas fa-cash-register', 'title' => 'Total Sales', 'query' => "SELECT COUNT(*) FROM COUNTER_SALE", 'color' => 'yellow']
    ];
} else {
    // Cashier statistics
    $stats = [
        ['icon' => 'fas fa-cash-register', 'title' => 'My Sales', 'query' => "SELECT COUNT(*) FROM COUNTER_SALE WHERE user_id = $user_id", 'color' => 'blue'],
        ['icon' => 'fas fa-file-prescription', 'title' => 'My Prescriptions', 'query' => "SELECT COUNT(*) FROM PRESCRIPTION WHERE user_id = $user_id", 'color' => 'purple'],
        ['icon' => 'fas fa-users', 'title' => 'Customers Today', 'query' => "SELECT COUNT(DISTINCT customer_id) FROM COUNTER_SALE WHERE DATE(sale_date) = CURRENT_DATE", 'color' => 'green']
    ];
}
?>

<div class="container mx-auto px-6 py-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-8 animate__animated animate__fadeIn">
        Welcome back, <?php echo htmlspecialchars($username); ?>
        <span class="block text-lg font-normal text-gray-600 mt-2">
            <?php echo $is_admin ? 'Administrator Dashboard' : 'Cashier Dashboard'; ?>
        </span>
    </h2>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-<?php echo $is_admin ? '5' : '3'; ?> gap-6 mb-12">
        <?php foreach ($stats as $stat): ?>
            <?php
            $stmt = $pdo->query($stat['query']);
            $count = $stmt->fetchColumn();
            ?>
            <div class="bg-white rounded-xl shadow-lg p-6 animate__animated animate__fadeInUp">
                <div class="flex items-center">
                    <div class="p-4 rounded-full bg-<?php echo $stat['color']; ?>-500 bg-opacity-75">
                        <i class="<?php echo $stat['icon']; ?> fa-2x text-white"></i>
                    </div>
                    <div class="ml-6">
                        <h4 class="text-3xl font-bold text-gray-700"><?php echo number_format($count); ?></h4>
                        <div class="text-gray-500 mt-1"><?php echo $stat['title']; ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-<?php echo $is_admin ? '3' : '2'; ?> gap-6 mb-8">
        <!-- Cashier Actions -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold">New Sale</h3>
                <i class="fas fa-cash-register text-3xl"></i>
            </div>
            <p class="mb-4">Create a new sale transaction</p>
            <a href="<?php echo $base_url; ?>sales/new_sale.php" class="inline-block bg-white text-blue-600 px-4 py-2 rounded-lg">
                Create Sale <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold">Prescriptions</h3>
                <i class="fas fa-file-prescription text-3xl"></i>
            </div>
            <p class="mb-4">Process prescriptions</p>
            <a href="<?php echo $base_url; ?>prescriptions/new_prescription.php" class="inline-block bg-white text-purple-600 px-4 py-2 rounded-lg">
                New Prescription <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <?php if ($is_admin): ?>
        <!-- Admin-only Actions -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold">Inventory</h3>
                <i class="fas fa-boxes text-3xl"></i>
            </div>
            <p class="mb-4">Manage inventory levels</p>
            <a href="<?php echo $base_url; ?>inventory/check_stock.php" class="inline-block bg-white text-green-600 px-4 py-2 rounded-lg">
                Check Stock <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($is_admin): ?>
    <!-- Admin-only Sections -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">System Overview</h3>
        <!-- Add admin-specific content -->
    </div>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>
