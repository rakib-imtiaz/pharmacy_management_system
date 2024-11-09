<?php
require_once '../includes/db_connect.php';
session_start();

// Redirect if not logged in or invalid role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Administrator', 'Supplier'])) {
    header("Location: ../login.php");
    exit();
}

$is_admin = ($_SESSION['role'] === 'Administrator');
$supplier_id = $is_admin ? null : $_SESSION['supplier_id'];

// Query to fetch invoices based on user role
if ($is_admin) {
    // Admins can see all invoices
    $query = "
        SELECT i.invoice_id, i.invoice_date, i.total_amount, i.status, s.name as supplier_name, u.username as created_by, i.supplier_id
        FROM invoice i
        JOIN supplier s ON i.supplier_id = s.supplier_id
        JOIN user u ON i.user_id = u.user_id
        ORDER BY i.invoice_date DESC
    ";
    $stmt = $pdo->query($query);
} else {
    // Suppliers see only their own invoices
    $query = "
        SELECT i.invoice_id, i.invoice_date, i.total_amount, i.status, s.name as supplier_name, u.username as created_by, i.supplier_id
        FROM invoice i
        JOIN supplier s ON i.supplier_id = s.supplier_id
        JOIN user u ON i.user_id = u.user_id
        WHERE i.supplier_id = ?
        ORDER BY i.invoice_date DESC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$supplier_id]);
}

$invoices = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Invoice Management</h1>
        <?php if ($is_admin || $_SESSION['role'] === 'Supplier'): ?>
            <a href="add_invoice.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-plus mr-2"></i>Add Invoice
            </a>
        <?php endif; ?>
    </div>

    <!-- Success and error messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
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
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo date('M d, Y', strtotime($invoice['invoice_date'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($invoice['supplier_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">৳<?php echo number_format($invoice['total_amount'], 2); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $invoice['status'] === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                    <?php echo $invoice['status']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($invoice['created_by']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex space-x-2">
                                    <a href="view_invoice.php?id=<?php echo $invoice['invoice_id']; ?>" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($is_admin || ($invoice['supplier_id'] == $_SESSION['supplier_id'])): ?>
                                        <a href="edit_invoice.php?id=<?php echo $invoice['invoice_id']; ?>" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($is_admin): ?>
                                        <form action="" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this invoice? This action cannot be undone.');">
                                            <input type="hidden" name="invoice_id" value="<?php echo $invoice['invoice_id']; ?>">
                                            <button type="submit" name="delete_invoice" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
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
