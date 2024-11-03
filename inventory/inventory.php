<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

// Get inventory items with related drug and supplier information
$inventory = $pdo->query("
    SELECT si.*, d.name as drug_name, s.name as supplier_name, 
           d.dosage_form, dc.name as category_name
    FROM STOCK_ITEM si
    JOIN DRUG d ON si.drug_id = d.drug_id
    JOIN SUPPLIER s ON si.supplier_id = s.supplier_id
    JOIN DRUG_CATEGORY dc ON d.category_id = dc.category_id
    ORDER BY si.stock_item_id DESC
")->fetchAll();
?>

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Inventory Management</h2>
        <a href="stock_add.php" class="bg-blue-500 text-white px-4 py-2 rounded">Add New Stock</a>
    </div>

    <!-- Inventory Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold text-gray-700">Total Items</h3>
            <p class="text-2xl font-bold text-blue-600">
                <?= $pdo->query("SELECT COUNT(*) FROM STOCK_ITEM")->fetchColumn() ?>
            </p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold text-gray-700">Low Stock Items</h3>
            <p class="text-2xl font-bold text-red-600">
                <?= $pdo->query("SELECT COUNT(*) FROM STOCK_ITEM WHERE quantity < 100")->fetchColumn() ?>
            </p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h3 class="text-lg font-semibold text-gray-700">Expiring Soon</h3>
            <p class="text-2xl font-bold text-yellow-600">
                <?= $pdo->query("SELECT COUNT(*) FROM STOCK_ITEM WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)")->fetchColumn() ?>
            </p>
        </div>
    </div>

    <div class="bg-white shadow-md rounded overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left">ID</th>
                    <th class="px-6 py-3 text-left">Drug</th>
                    <th class="px-6 py-3 text-left">Category</th>
                    <th class="px-6 py-3 text-left">Supplier</th>
                    <th class="px-6 py-3 text-left">Quantity</th>
                    <th class="px-6 py-3 text-left">Unit Price</th>
                    <th class="px-6 py-3 text-left">Expiry Date</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventory as $item): ?>
                <tr class="border-b <?= strtotime($item['expiry_date']) <= strtotime('+30 days') ? 'bg-red-50' : '' ?>">
                    <td class="px-6 py-4"><?= htmlspecialchars($item['stock_item_id']) ?></td>
                    <td class="px-6 py-4">
                        <?= htmlspecialchars($item['drug_name']) ?>
                        <span class="text-sm text-gray-500 block"><?= htmlspecialchars($item['dosage_form']) ?></span>
                    </td>
                    <td class="px-6 py-4"><?= htmlspecialchars($item['category_name']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($item['supplier_name']) ?></td>
                    <td class="px-6 py-4">
                        <span class="<?= $item['quantity'] < 100 ? 'text-red-600 font-bold' : '' ?>">
                            <?= htmlspecialchars($item['quantity']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">$<?= number_format($item['unit_price'], 2) ?></td>
                    <td class="px-6 py-4">
                        <span class="<?= strtotime($item['expiry_date']) <= strtotime('+30 days') ? 'text-red-600 font-bold' : '' ?>">
                            <?= date('Y-m-d', strtotime($item['expiry_date'])) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="stock_edit.php?id=<?= $item['stock_item_id'] ?>" class="text-blue-500">Edit</a>
                        <a href="stock_delete.php?id=<?= $item['stock_item_id'] ?>" 
                           class="text-red-500 ml-2" 
                           onclick="return confirm('Are you sure you want to delete this stock item?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 