<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

// Get prescriptions with related customer and doctor information
$prescriptions = $pdo->query("
    SELECT p.*, 
           c.name as customer_name,
           d.name as doctor_name,
           COUNT(pi.prescription_item_id) as item_count
    FROM PRESCRIPTION p
    JOIN CUSTOMER c ON p.customer_id = c.customer_id
    JOIN DOCTOR d ON p.doctor_id = d.doctor_id
    LEFT JOIN PRESCRIPTION_ITEM pi ON p.prescription_id = pi.prescription_id
    GROUP BY p.prescription_id
    ORDER BY p.prescription_date DESC
")->fetchAll();
?>

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Prescriptions Management</h2>
        <a href="prescription_add.php" class="bg-blue-500 text-white px-4 py-2 rounded">New Prescription</a>
    </div>

    <!-- Status Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold text-gray-700">Total Prescriptions</h3>
            <p class="text-2xl font-bold text-blue-600">
                <?= $pdo->query("SELECT COUNT(*) FROM PRESCRIPTION")->fetchColumn() ?>
            </p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold text-gray-700">Pending</h3>
            <p class="text-2xl font-bold text-yellow-600">
                <?= $pdo->query("SELECT COUNT(*) FROM PRESCRIPTION WHERE status = 'Pending'")->fetchColumn() ?>
            </p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold text-gray-700">Filled</h3>
            <p class="text-2xl font-bold text-green-600">
                <?= $pdo->query("SELECT COUNT(*) FROM PRESCRIPTION WHERE status = 'Filled'")->fetchColumn() ?>
            </p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold text-gray-700">Today's Prescriptions</h3>
            <p class="text-2xl font-bold text-purple-600">
                <?= $pdo->query("SELECT COUNT(*) FROM PRESCRIPTION WHERE DATE(prescription_date) = CURDATE()")->fetchColumn() ?>
            </p>
        </div>
    </div>

    <div class="bg-white shadow-md rounded overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left">ID</th>
                    <th class="px-6 py-3 text-left">Date</th>
                    <th class="px-6 py-3 text-left">Customer</th>
                    <th class="px-6 py-3 text-left">Doctor</th>
                    <th class="px-6 py-3 text-left">Items</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $prescription): ?>
                <tr class="border-b">
                    <td class="px-6 py-4"><?= htmlspecialchars($prescription['prescription_id']) ?></td>
                    <td class="px-6 py-4"><?= date('Y-m-d', strtotime($prescription['prescription_date'])) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($prescription['customer_name']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($prescription['doctor_name']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($prescription['item_count']) ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded text-sm
                            <?php
                            switch($prescription['status']) {
                                case 'Pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                case 'Filled': echo 'bg-green-100 text-green-800'; break;
                                default: echo 'bg-gray-100 text-gray-800';
                            }
                            ?>">
                            <?= htmlspecialchars($prescription['status']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="prescription_items.php?id=<?= $prescription['prescription_id'] ?>" 
                           class="text-blue-500">View Items</a>
                        <a href="prescription_edit.php?id=<?= $prescription['prescription_id'] ?>" 
                           class="text-blue-500 ml-2">Edit</a>
                        <a href="prescription_delete.php?id=<?= $prescription['prescription_id'] ?>" 
                           class="text-red-500 ml-2" 
                           onclick="return confirm('Are you sure you want to delete this prescription?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 