<?php
require_once '../includes/db_connect.php';
session_start();

// Redirect if not logged in or invalid role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Administrator', 'Supplier'])) {
    header("Location: " . $base_url . "login.php");
    exit();
}

$is_admin = ($_SESSION['role'] === 'Administrator');
$invoice_id = $_GET['id'];

// Fetch invoice details with supplier and user information
$invoice_query = "
    SELECT 
        i.*,
        s.name AS supplier_name,
        s.contact_info AS supplier_contact,
        u.username AS created_by
    FROM INVOICE i
    JOIN SUPPLIER s ON i.supplier_id = s.supplier_id
    JOIN USER u ON i.user_id = u.user_id
    WHERE i.invoice_id = ?
";
$stmt = $pdo->prepare($invoice_query);
$stmt->execute([$invoice_id]);
$invoice = $stmt->fetch();

// Redirect if invoice not found or supplier tries to access another supplier's invoice
if (!$invoice || (!$is_admin && $invoice['supplier_id'] != $_SESSION['supplier_id'])) {
    $_SESSION['error'] = "You are not authorized to view this invoice.";
    header("Location: " . $base_url . "invoices/invoices.php");
    exit();
}

// Fetch invoice items
$items_query = "
    SELECT 
        ii.*,
        si.unit_price AS stock_unit_price,
        d.name AS drug_name,
        d.dosage_form
    FROM INVOICE_ITEM ii
    JOIN STOCK_ITEM si ON ii.stock_item_id = si.stock_item_id
    JOIN DRUG d ON si.drug_id = d.drug_id
    WHERE ii.invoice_id = ?
";
$items_stmt = $pdo->prepare($items_query);
$items_stmt->execute([$invoice_id]);
$invoice_items = $items_stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Invoice Details #<?php echo $invoice_id; ?></h1>
            <div class="space-x-2">
                <?php if ($is_admin): ?>
                    <a href="<?php echo $base_url; ?>invoices/edit_invoice.php?id=<?php echo $invoice_id; ?>" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                <?php endif; ?>
                <a href="<?php echo $base_url; ?>invoices/invoices.php" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>

        <!-- Invoice Header -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Invoice Information</h3>
                    <div class="space-y-2">
                        <p><span class="font-semibold">Date:</span> 
                            <?php echo date('F d, Y', strtotime($invoice['invoice_date'])); ?>
                        </p>
                        <p><span class="font-semibold">Status:</span> 
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php echo $invoice['status'] === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                <?php echo $invoice['status']; ?>
                            </span>
                        </p>
                        <p><span class="font-semibold">Created By:</span> 
                            <?php echo htmlspecialchars($invoice['created_by']); ?>
                        </p>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Supplier Information</h3>
                    <div class="space-y-2">
                        <p><span class="font-semibold">Name:</span> 
                            <?php echo htmlspecialchars($invoice['supplier_name']); ?>
                        </p>
                        <p><span class="font-semibold">Contact:</span> 
                            <?php echo htmlspecialchars($invoice['supplier_contact']); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold">Invoice Items</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($invoice_items as $item): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($item['drug_name'] . ' (' . $item['dosage_form'] . ')'); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo $item['quantity']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    ৳<?php echo number_format($item['stock_unit_price'], 2); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    ৳<?php echo number_format($item['quantity'] * $item['stock_unit_price'], 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right font-semibold">Total Amount:</td>
                            <td class="px-6 py-4 whitespace-nowrap font-semibold">
                                ৳<?php echo number_format($invoice['total_amount'], 2); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <?php if ($is_admin && $invoice['status'] !== 'Paid'): ?>
            <!-- Payment Actions (Admin Only) -->
            <div class="mt-6 flex justify-end">
                <form action="process_payment.php" method="POST" class="inline-block">
                    <input type="hidden" name="invoice_id" value="<?php echo $invoice_id; ?>">
                    <button type="submit" name="mark_as_paid" 
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition"
                            onclick="return confirm('Are you sure you want to mark this invoice as paid?')">
                        <i class="fas fa-check mr-2"></i>Mark as Paid
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
