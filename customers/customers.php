<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

$customers = $pdo->query("SELECT * FROM CUSTOMER ORDER BY customer_id DESC")->fetchAll();
?>

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Customers</h2>
        <a href="customer_add.php" class="bg-blue-500 text-white px-4 py-2 rounded">Add New Customer</a>
    </div>

    <div class="bg-white shadow-md rounded">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left">ID</th>
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Contact Info</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                <tr class="border-b">
                    <td class="px-6 py-4"><?= htmlspecialchars($customer['customer_id']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($customer['name']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($customer['contact_info']) ?></td>
                    <td class="px-6 py-4">
                        <a href="customer_edit.php?id=<?= $customer['customer_id'] ?>" class="text-blue-500">Edit</a>
                        <a href="customer_delete.php?id=<?= $customer['customer_id'] ?>" class="text-red-500 ml-2" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 