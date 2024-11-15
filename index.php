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
$is_supplier = ($role === 'Supplier');
$is_doctor = ($role === 'Doctor');
$is_cashier = ($role === 'Cashier');

// User Activity variables
try {
    // Total Users
    $stmt = $pdo->query("SELECT COUNT(*) FROM USER");
    $total_users = $stmt->fetchColumn() ?: 0;

    // Active Users (last 5 minutes based on last_login)
    $stmt = $pdo->query("SELECT COUNT(*) FROM USER WHERE last_login >= NOW() - INTERVAL 5 MINUTE");
    $active_users = $stmt->fetchColumn() ?: 0;

} catch (PDOException $e) {
    $total_users = $active_users = 0;
    error_log("User Activity Query Error: " . $e->getMessage());
}

// Sales Summary variables
try {
    // Today's Sales
    $stmt = $pdo->query("SELECT COUNT(*) FROM COUNTER_SALE WHERE DATE(sale_date) = CURRENT_DATE");
    $todays_sales = $stmt->fetchColumn() ?: 0;

    // This Week's Sales
    $stmt = $pdo->query("SELECT COUNT(*) FROM COUNTER_SALE WHERE WEEK(sale_date) = WEEK(CURRENT_DATE)");
    $weekly_sales = $stmt->fetchColumn() ?: 0;

    // Total Revenue
    $stmt = $pdo->query("SELECT SUM(total_amount) FROM COUNTER_SALE");
    $total_revenue = $stmt->fetchColumn() ?: 0.00;
} catch (PDOException $e) {
    $todays_sales = $weekly_sales = 0;
    $total_revenue = 0.00;
    error_log("Sales Summary Query Error: " . $e->getMessage());
}

// Inventory Status variables
try {
    // Low Stock Items
    $stmt = $pdo->query("SELECT COUNT(*) FROM DRUG WHERE stock < 10");
    $low_stock_items = $stmt->fetchColumn() ?: 0;

    // Out of Stock Items
    $stmt = $pdo->query("SELECT COUNT(*) FROM DRUG WHERE stock = 0");
    $out_of_stock_items = $stmt->fetchColumn() ?: 0;
} catch (PDOException $e) {
    $low_stock_items = $out_of_stock_items = 0;
    error_log("Inventory Status Query Error: " . $e->getMessage());
}

// Recent Activity Log
try {
    $stmt = $pdo->query("SELECT action FROM AUDIT_LOG ORDER BY timestamp DESC LIMIT 5");
    $recent_activities = $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
} catch (PDOException $e) {
    $recent_activities = [];
    error_log("Recent Activity Log Query Error: " . $e->getMessage());
}

// Define stats for dashboard based on role
$stats = [];
if ($is_admin) {
    $stats = [
        ['icon' => 'fas fa-pills', 'title' => 'Total Drugs', 'query' => "SELECT COUNT(*) FROM DRUG", 'color' => 'blue'],
        ['icon' => 'fas fa-user-md', 'title' => 'Doctors', 'query' => "SELECT COUNT(*) FROM DOCTOR", 'color' => 'indigo'],
        ['icon' => 'fas fa-users', 'title' => 'Customers', 'query' => "SELECT COUNT(*) FROM CUSTOMER", 'color' => 'green'],
        ['icon' => 'fas fa-file-prescription', 'title' => 'Prescriptions', 'query' => "SELECT COUNT(*) FROM PRESCRIPTION", 'color' => 'purple'],
        ['icon' => 'fas fa-cash-register', 'title' => 'Total Sales', 'query' => "SELECT COUNT(*) FROM COUNTER_SALE", 'color' => 'yellow']
    ];
} elseif ($is_supplier) {
    $stats = [
        ['icon' => 'fas fa-box', 'title' => 'Supplied Products', 'query' => "SELECT COUNT(*) FROM DRUG WHERE supplier_id = $user_id", 'color' => 'green'],
        ['icon' => 'fas fa-truck', 'title' => 'Stock Items', 'query' => "SELECT COUNT(*) FROM stock_item WHERE supplier_id = $user_id", 'color' => 'blue']
    ];
} elseif ($is_doctor) {
    $stats = [
        ['icon' => 'fas fa-file-prescription', 'title' => 'My Prescriptions', 'query' => "SELECT COUNT(*) FROM PRESCRIPTION WHERE doctor_id = $user_id", 'color' => 'purple']
    ];
} elseif ($is_cashier) {
    $stats = [
        ['icon' => 'fas fa-cash-register', 'title' => 'My Sales', 'query' => "SELECT COUNT(*) FROM COUNTER_SALE WHERE user_id = $user_id", 'color' => 'blue']
    ];
}

?>

<div class="container mx-auto px-6 py-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-8 animate__animated animate__fadeIn">
        Welcome back, <?php echo htmlspecialchars($username); ?>
        <span class="block text-lg font-normal text-gray-600 mt-2">
            <?php 
            if ($is_admin) {
                echo 'Administrator Dashboard';
            } elseif ($is_supplier) {
                echo 'Supplier Dashboard';
            } elseif ($is_doctor) {
                echo 'Doctor Dashboard';
            } elseif ($is_cashier) {
                echo 'Cashier Dashboard';
            }
            ?>
        </span>
    </h2>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-<?php echo $is_admin ? '5' : ($is_supplier ? '2' : ($is_doctor || $is_cashier ? '1' : '3')); ?> gap-6 mb-12">
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
    <div class="grid grid-cols-1 md:grid-cols-<?php echo $is_admin ? '3' : ($is_supplier ? '2' : '2'); ?> gap-6 mb-8">
        <?php if ($is_cashier): ?>
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
        <?php endif; ?>

        <?php if ($is_doctor): ?>
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">My Prescriptions</h3>
                    <i class="fas fa-file-prescription text-3xl"></i>
                </div>
                <p class="mb-4">View and manage prescriptions you've issued</p>
                <a href="<?php echo $base_url; ?>prescriptions/my_prescriptions.php" class="inline-block bg-white text-purple-600 px-4 py-2 rounded-lg">
                    View Prescriptions <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Inventory</h3>
                    <i class="fas fa-boxes text-3xl"></i>
                </div>
                <p class="mb-4">Manage inventory levels</p>
                <a href="<?php echo $base_url; ?>inventory/inventory.php" class="inline-block bg-white text-green-600 px-4 py-2 rounded-lg">
                    Check Stock <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
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
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Update Stock</h3>
                    <i class="fas fa-box text-3xl"></i>
                </div>
                <p class="mb-4">View and manage your supplied products</p>
                <a href="<?php echo $base_url; ?>inventory/inventory.php" class="inline-block bg-white text-orange-600 px-4 py-2 rounded-lg">
                    Update Stock <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($is_supplier): ?>
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold">Update Stock</h3>
                    <i class="fas fa-box text-3xl"></i>
                </div>
                <p class="mb-4">View and manage your supplied products</p>
                <a href="<?php echo $base_url; ?>inventory/inventory.php" class="inline-block bg-white text-orange-600 px-4 py-2 rounded-lg">
                    Update Stock <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($is_admin): ?>
        <!-- Admin-only Sections -->
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">System Overview</h3>

            <div class="flex justify-between items-center mb-6">
                <div class="flex flex-col items-start">
                    <h4 class="text-xl font-semibold text-gray-600">User Activity</h4>
                    <p class="text-sm text-gray-500">Total Users: <strong><?php echo $total_users; ?></strong></p>
                    <p class="text-sm text-gray-500">Active Users: <strong><?php echo $active_users; ?></strong></p>
                </div>
            </div>

            <div class="flex justify-between items-center mb-6">
                <div class="flex flex-col items-start">
                    <h4 class="text-xl font-semibold text-gray-600">Sales Summary</h4>
                    <p class="text-sm text-gray-500">Today's Sales: <strong><?php echo $todays_sales; ?></strong></p>
                    <p class="text-sm text-gray-500">This Week's Sales: <strong><?php echo $weekly_sales; ?></strong></p>
                    <p class="text-sm text-gray-500">Total Revenue: <strong><?php echo number_format($total_revenue, 2); ?></strong></p>
                </div>
            </div>

            <div class="flex justify-between items-center mb-6">
                <div class="flex flex-col items-start">
                    <h4 class="text-xl font-semibold text-gray-600">Inventory Status</h4>
                    <p class="text-sm text-gray-500">Low Stock Items: <strong><?php echo $low_stock_items; ?></strong></p>
                    <p class="text-sm text-gray-500">Out of Stock: <strong><?php echo $out_of_stock_items; ?></strong></p>
                </div>
            </div>

            <div class="flex flex-col">
                <h4 class="text-xl font-semibold text-gray-600">Alerts & Notifications</h4>
                <p class="text-sm text-gray-500">System is Normal</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>
