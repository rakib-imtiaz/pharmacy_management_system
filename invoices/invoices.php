<?php
require_once '../includes/db_connect.php';
session_start();

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header("Location: " . $base_url . "login.php");
    exit();
}

// Handle invoice deletion
if (isset($_POST['delete_invoice'])) {
    $invoice_id = $_POST['invoice_id'];
    try {
        $pdo->beginTransaction();

        // First check if invoice has payments
        $check_payments = $pdo->prepare("SELECT COUNT(*) FROM PAYMENT WHERE invoice_id = ?");
        $check_payments->execute([$invoice_id]);
        $has_payments = $check_payments->fetchColumn() > 0;

        if ($has_payments) {
            // Delete related payments first
            $delete_payments = $pdo->prepare("DELETE FROM PAYMENT WHERE invoice_id = ?");
            $delete_payments->execute([$invoice_id]);
        }

        // Then delete invoice items
        $delete_items = $pdo->prepare("DELETE FROM INVOICE_ITEM WHERE invoice_id = ?");
        $delete_items->execute([$invoice_id]);
        
        // Finally delete the invoice
        $delete_invoice = $pdo->prepare("DELETE FROM INVOICE WHERE invoice_id = ?");
        $delete_invoice->execute([$invoice_id]);
        
        $pdo->commit();
        $_SESSION['success'] = "Invoice and all related records deleted successfully";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error deleting invoice: " . $e->getMessage();
    }
    header("Location: " . $base_url . "invoices/invoices.php");
    exit();
}

// Fetch all invoices with supplier information
$query = "
    SELECT 
        i.invoice_id,
        i.invoice_date,
        i.total_amount,
        i.status,
        s.name as supplier_name,
        u.username as created_by
    FROM INVOICE i
    JOIN SUPPLIER s ON i.supplier_id = s.supplier_id
    JOIN USER u ON i.user_id = u.user_id
    ORDER BY i.invoice_date DESC
";

$invoices = $pdo->query($query)->fetchAll();

require_once '../includes/header.php';
?>

<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Invoice Management</h1>
        <a href="<?php echo $base_url; ?>invoices/add_invoice.php" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
            <i class="fas fa-plus mr-2"></i>Add Invoice
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($invoices as $invoice): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo $invoice['invoice_id']; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php echo date('M d, Y', strtotime($invoice['invoice_date'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($invoice['supplier_name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">৳<?php echo number_format($invoice['total_amount'], 2); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php echo $invoice['status'] === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                <?php echo $invoice['status']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($invoice['created_by']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex space-x-2">
                                <a href="<?php echo $base_url; ?>invoices/view_invoice.php?id=<?php echo $invoice['invoice_id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo $base_url; ?>invoices/edit_invoice.php?id=<?php echo $invoice['invoice_id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="" method="POST" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this invoice? This action cannot be undone.');">
                                    <input type="hidden" name="invoice_id" value="<?php echo $invoice['invoice_id']; ?>">
                                    <button type="submit" name="delete_invoice" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 