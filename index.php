<?php
require_once 'includes/db_connect.php';
require_once 'includes/header.php';

// Get some basic statistics
$stats = [
    'drugs' => $pdo->query("SELECT COUNT(*) FROM DRUG")->fetchColumn(),
    'customers' => $pdo->query("SELECT COUNT(*) FROM CUSTOMER")->fetchColumn(),
    'prescriptions' => $pdo->query("SELECT COUNT(*) FROM PRESCRIPTION")->fetchColumn(),
    'sales' => $pdo->query("SELECT COUNT(*) FROM COUNTER_SALE")->fetchColumn()
];
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Statistics Cards -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-bold text-blue-600">Drugs</h3>
        <p class="text-3xl font-bold"><?php echo $stats['drugs']; ?></p>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-bold text-blue-600">Customers</h3>
        <p class="text-3xl font-bold"><?php echo $stats['customers']; ?></p>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-bold text-blue-600">Prescriptions</h3>
        <p class="text-3xl font-bold"><?php echo $stats['prescriptions']; ?></p>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-bold text-blue-600">Sales</h3>
        <p class="text-3xl font-bold"><?php echo $stats['sales']; ?></p>
    </div>
</div>

<div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Quick Actions -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
        <div class="space-y-2">
            <a href="customers/customer_add.php" class="block bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Add New Customer</a>
            <a href="prescriptions/prescription_add.php" class="block bg-green-500 text-white p-2 rounded hover:bg-green-600">Create Prescription</a>
            <a href="sales/sale_add.php" class="block bg-purple-500 text-white p-2 rounded hover:bg-purple-600">New Sale</a>
        </div>
    </div>
    
    <!-- Recent Activities -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-bold mb-4">Recent Activities</h2>
        <?php
        $recent_activities = $pdo->query("
            SELECT action, table_affected, timestamp 
            FROM AUDIT_LOG 
            ORDER BY timestamp DESC 
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($recent_activities as $activity): ?>
            <div class="border-b py-2">
                <p class="text-sm">
                    <?php echo htmlspecialchars($activity['action']); ?> in 
                    <?php echo htmlspecialchars($activity['table_affected']); ?>
                    <span class="text-gray-500">
                        (<?php echo date('M d, Y H:i', strtotime($activity['timestamp'])); ?>)
                    </span>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
