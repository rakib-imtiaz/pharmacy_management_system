<?php
require_once '../includes/db_connect.php';
session_start();
include_once '../includes/header.php';

// Fetch sales with related information
$query = "
    SELECT 
        cs.sale_id,
        cs.sale_date,
        cs.total_amount,
        c.name as customer_name,
        u.username as cashier_name,
        COUNT(csi.sale_item_id) as total_items
    FROM COUNTER_SALE cs
    LEFT JOIN CUSTOMER c ON cs.customer_id = c.customer_id
    LEFT JOIN USER u ON cs.user_id = u.user_id
    LEFT JOIN COUNTER_SALE_ITEM csi ON cs.sale_id = csi.sale_id
    GROUP BY cs.sale_id
    ORDER BY cs.sale_date DESC
";

$sales = $pdo->query($query)->fetchAll();

// Fetch customers for dropdown
$customers = $pdo->query("SELECT customer_id, name FROM CUSTOMER ORDER BY name")->fetchAll();

// Fetch available stock items with drug information
$stock_query = "
    SELECT 
        si.stock_item_id,
        si.quantity as available_quantity,
        si.unit_price,
        si.expiry_date,
        d.name as drug_name,
        d.dosage_form
    FROM STOCK_ITEM si
    JOIN DRUG d ON si.drug_id = d.drug_id
    WHERE si.quantity > 0 AND si.expiry_date > CURRENT_DATE
    ORDER BY d.name
";
$stock_items = $pdo->query($stock_query)->fetchAll();
?>

<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Sales Management</h2>
        <button onclick="openNewSaleModal()" 
                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>New Sale
        </button>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cashier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($sales as $sale): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo $sale['sale_id']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php echo date('M d, Y', strtotime($sale['sale_date'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php echo htmlspecialchars($sale['customer_name'] ?? 'Walk-in Customer'); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo $sale['total_items']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            ৳<?php echo number_format($sale['total_amount'], 2); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php echo htmlspecialchars($sale['cashier_name']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewSaleDetails(<?php echo $sale['sale_id']; ?>)" 
                                    class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- New Sale Modal -->
<div id="newSaleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-3/4 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">New Sale</h3>
            <form id="newSaleForm" method="POST" action="process_sale.php">
                <!-- Customer Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Customer (Optional)</label>
                    <select name="customer_id" class="w-full rounded-lg border-gray-300">
                        <option value="">Walk-in Customer</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['customer_id']; ?>">
                                <?php echo htmlspecialchars($customer['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Sale Items -->
                <div class="mb-4">
                    <h4 class="text-md font-medium text-gray-700 mb-2">Sale Items</h4>
                    <div id="saleItems">
                        <div class="grid grid-cols-4 gap-4 mb-2 sale-item">
                            <div>
                                <select name="items[0][stock_item_id]" required class="w-full rounded-lg border-gray-300"
                                        onchange="updatePrice(this, 0)">
                                    <option value="">Select Item</option>
                                    <?php foreach ($stock_items as $item): ?>
                                        <option value="<?php echo $item['stock_item_id']; ?>"
                                                data-price="<?php echo $item['unit_price']; ?>"
                                                data-max="<?php echo $item['available_quantity']; ?>">
                                            <?php echo htmlspecialchars($item['drug_name'] . ' (' . $item['dosage_form'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <input type="number" name="items[0][quantity]" required min="1" 
                                       class="w-full rounded-lg border-gray-300"
                                       onchange="updateSubtotal(0)" placeholder="Quantity">
                            </div>
                            <div>
                                <input type="number" name="items[0][unit_price]" required readonly 
                                       class="w-full rounded-lg border-gray-300" placeholder="Unit Price">
                            </div>
                            <div>
                                <input type="number" name="items[0][subtotal]" readonly 
                                       class="w-full rounded-lg border-gray-300" placeholder="Subtotal">
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="addSaleItem()" 
                            class="mt-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Add Item
                    </button>
                </div>

                <!-- Total Amount -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Amount</label>
                    <input type="number" name="total_amount" readonly 
                           class="w-full rounded-lg border-gray-300" id="totalAmount">
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeNewSaleModal()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Process Sale
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let itemCount = 1;

function openNewSaleModal() {
    document.getElementById('newSaleModal').classList.remove('hidden');
}

function closeNewSaleModal() {
    document.getElementById('newSaleModal').classList.add('hidden');
}

function addSaleItem() {
    const container = document.getElementById('saleItems');
    const template = `
        <div class="grid grid-cols-4 gap-4 mb-2 sale-item">
            <div>
                <select name="items[${itemCount}][stock_item_id]" required class="w-full rounded-lg border-gray-300"
                        onchange="updatePrice(this, ${itemCount})">
                    <option value="">Select Item</option>
                    <?php foreach ($stock_items as $item): ?>
                        <option value="<?php echo $item['stock_item_id']; ?>"
                                data-price="<?php echo $item['unit_price']; ?>"
                                data-max="<?php echo $item['available_quantity']; ?>">
                            <?php echo htmlspecialchars($item['drug_name'] . ' (' . $item['dosage_form'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <input type="number" name="items[${itemCount}][quantity]" required min="1" 
                       class="w-full rounded-lg border-gray-300"
                       onchange="updateSubtotal(${itemCount})" placeholder="Quantity">
            </div>
            <div>
                <input type="number" name="items[${itemCount}][unit_price]" required readonly 
                       class="w-full rounded-lg border-gray-300" placeholder="Unit Price">
            </div>
            <div>
                <input type="number" name="items[${itemCount}][subtotal]" readonly 
                       class="w-full rounded-lg border-gray-300" placeholder="Subtotal">
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', template);
    itemCount++;
}

function updatePrice(select, index) {
    const option = select.options[select.selectedIndex];
    const price = option.dataset.price;
    const priceInput = document.querySelector(`input[name="items[${index}][unit_price]"]`);
    priceInput.value = price;
    updateSubtotal(index);
}

function updateSubtotal(index) {
    const quantity = document.querySelector(`input[name="items[${index}][quantity]"]`).value;
    const price = document.querySelector(`input[name="items[${index}][unit_price]"]`).value;
    const subtotalInput = document.querySelector(`input[name="items[${index}][subtotal]"]`);
    subtotalInput.value = (quantity * price).toFixed(2);
    updateTotal();
}

function updateTotal() {
    const subtotals = document.querySelectorAll('input[name$="[subtotal]"]');
    let total = 0;
    subtotals.forEach(input => {
        total += parseFloat(input.value || 0);
    });
    document.getElementById('totalAmount').value = total.toFixed(2);
}

function viewSaleDetails(saleId) {
    window.location.href = 'view_sale.php?id=' + saleId;
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('newSaleModal');
    if (event.target == modal) {
        closeNewSaleModal();
    }
}
</script>

<?php include_once '../includes/footer.php'; ?> 