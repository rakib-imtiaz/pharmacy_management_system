<?php
require_once '../includes/db_connect.php';
session_start();

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header("Location: ../index.php");
    exit;
}

include_once '../includes/header.php';

// Handle supplier actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_supplier'])) {
            $stmt = $pdo->prepare("
                INSERT INTO SUPPLIER (name, contact_person, contact_info, address) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $_POST['name'],
                $_POST['contact_person'],
                $_POST['contact_info'],
                $_POST['address']
            ]);
            $_SESSION['success'] = "Supplier added successfully";
        }

        if (isset($_POST['delete_supplier'])) {
            $stmt = $pdo->prepare("DELETE FROM SUPPLIER WHERE supplier_id = ?");
            $stmt->execute([$_POST['supplier_id']]);
            $_SESSION['success'] = "Supplier deleted successfully";
        }

        header("Location: suppliers.php");
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Fetch suppliers with their invoice summaries
$query = "
    SELECT 
        s.*,
        COUNT(DISTINCT i.invoice_id) as total_invoices,
        SUM(i.total_amount) as total_amount,
        MAX(i.invoice_date) as last_invoice_date
    FROM SUPPLIER s
    LEFT JOIN INVOICE i ON s.supplier_id = i.supplier_id
    GROUP BY s.supplier_id
    ORDER BY s.name
";
$suppliers = $pdo->query($query)->fetchAll();
?>

<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Supplier Management</h1>
        
        <!-- Add Supplier Button -->
        <button onclick="openAddModal()" 
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
            <i class="fas fa-plus mr-2"></i>Add Supplier
        </button>
    </div>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['success']); ?></span>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Suppliers Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Person</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Invoices</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($suppliers as $supplier): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900"><?php echo htmlspecialchars($supplier['name']); ?></div>
                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($supplier['address']); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($supplier['contact_person']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($supplier['contact_info']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo number_format($supplier['total_invoices']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">$<?php echo number_format($supplier['total_amount'], 2); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php echo $supplier['last_invoice_date'] ? date('M d, Y', strtotime($supplier['last_invoice_date'])) : 'N/A'; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex space-x-2">
                                <button onclick="viewInvoices(<?php echo $supplier['supplier_id']; ?>)"
                                        class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-file-invoice"></i>
                                </button>
                                <button onclick="editSupplier(<?php echo $supplier['supplier_id']; ?>)"
                                        class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
                                    <input type="hidden" name="supplier_id" value="<?php echo $supplier['supplier_id']; ?>">
                                    <button type="submit" name="delete_supplier" class="text-red-600 hover:text-red-900">
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

<!-- Add Supplier Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Supplier</h3>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Contact Person</label>
                    <input type="text" name="contact_person" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Contact Info</label>
                    <input type="text" name="contact_info" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea name="address" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeAddModal()"
                            class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit" name="add_supplier"
                            class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        Add Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}

function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}

function viewInvoices(supplierId) {
    window.location.href = `supplier_invoices.php?id=${supplierId}`;
}

function editSupplier(supplierId) {
    // Implement edit functionality
    alert('Edit supplier ' + supplierId);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('addModal');
    if (event.target == modal) {
        closeAddModal();
    }
}
</script>

