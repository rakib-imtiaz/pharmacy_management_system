<?php
require_once '../includes/db_connect.php';
session_start();
include_once '../includes/header.php';

// Get sale ID from URL
$sale_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch sale details with customer and cashier information
$query = "
    SELECT 
        cs.*,
        c.name as customer_name,
        c.contact_info as customer_contact,
        u.username as cashier_name
    FROM COUNTER_SALE cs
    LEFT JOIN CUSTOMER c ON cs.customer_id = c.customer_id
    JOIN USER u ON cs.user_id = u.user_id
    WHERE cs.sale_id = ?
";

$stmt = $pdo->prepare($query);
$stmt->execute([$sale_id]);
$sale = $stmt->fetch();

if (!$sale) {
    $_SESSION['error'] = "Sale not found";
    header("Location: sales.php");
    exit;
}

// Fetch sale items with drug information
$items_query = "
    SELECT 
        csi.*,
        si.expiry_date,
        d.name as drug_name,
        d.dosage_form
    FROM COUNTER_SALE_ITEM csi
    JOIN STOCK_ITEM si ON csi.stock_item_id = si.stock_item_id
    JOIN DRUG d ON si.drug_id = d.drug_id
    WHERE csi.sale_id = ?
";

$stmt = $pdo->prepare($items_query);
$stmt->execute([$sale_id]);
$items = $stmt->fetchAll();
?>

<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Sale Details #<?php echo $sale_id; ?></h2>
            <p class="text-gray-600 mt-2">
                Date: <?php echo date('M d, Y', strtotime($sale['sale_date'])); ?>
            </p>
        </div>
        <a href="sales.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Sales
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Customer Information -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-semibold mb-4">Customer Information</h3>
            <div class="space-y-2">
                <p><span class="font-medium">Name:</span> 
                    <?php echo htmlspecialchars($sale['customer_name'] ?? 'Walk-in Customer'); ?>
                </p>
                <?php if ($sale['customer_contact']): ?>
                    <p><span class="font-medium">Contact:</span> 
                        <?php echo htmlspecialchars($sale['customer_contact']); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sale Summary -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-semibold mb-4">Sale Summary</h3>
            <div class="space-y-2">
                <p><span class="font-medium">Total Amount:</span> 
                    $<?php echo number_format($sale['total_amount'], 2); ?>
                </p>
                <p><span class="font-medium">Cashier:</span> 
                    <?php echo htmlspecialchars($sale['cashier_name']); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Sale Items -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold">Sale Items</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="px-6 py-4">
                            <?php echo htmlspecialchars($item['drug_name'] . ' (' . $item['dosage_form'] . ')'); ?>
                        </td>
                        <td class="px-6 py-4"><?php echo $item['quantity']; ?></td>
                        <td class="px-6 py-4">$<?php echo number_format($item['unit_price'], 2); ?></td>
                        <td class="px-6 py-4">
                            $<?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php echo date('M d, Y', strtotime($item['expiry_date'])); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-6 py-4 text-right font-medium">Total:</td>
                    <td class="px-6 py-4 font-medium">
                        $<?php echo number_format($sale['total_amount'], 2); ?>
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Print Button -->
    <div class="mt-6 flex justify-end">
        <button onclick="printReceipt()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
            <i class="fas fa-print mr-2"></i>Print Receipt
        </button>
    </div>
</div>

<script>
function printReceipt() {
    window.print();
}
</script>

<?php include_once '../includes/footer.php'; ?> 