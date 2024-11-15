<?php
require_once '../includes/db_connect.php';
session_start();
include_once '../includes/header.php';

// Fetch customers with additional statistics
$query = "
    SELECT 
        c.*,
        COUNT(DISTINCT p.prescription_id) as total_prescriptions,
        COUNT(DISTINCT cs.sale_id) as total_sales,
        COALESCE(SUM(cs.total_amount), 0) as total_spent
    FROM CUSTOMER c
    LEFT JOIN PRESCRIPTION p ON c.customer_id = p.customer_id
    LEFT JOIN COUNTER_SALE cs ON c.customer_id = cs.customer_id
    GROUP BY c.customer_id
    ORDER BY c.name
";

$customers = $pdo->query($query)->fetchAll();
?>

<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800">Customer Management</h2>
        <button onclick="openAddCustomerModal()" 
                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>Add Customer
        </button>
    </div>

    <!-- Customer Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registration Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prescriptions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($customers as $customer): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php echo htmlspecialchars($customer['name']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php echo htmlspecialchars($customer['contact_info']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php echo date('M d, Y', strtotime($customer['registration_date'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php echo $customer['total_prescriptions']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php echo $customer['total_sales']; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            $<?php echo number_format($customer['total_spent'], 2); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="edit_customer.php?id=<?php echo $customer['customer_id']; ?>" 
                               class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i> 
                            </a>
                            <a href="customer_history.php?id=<?php echo $customer['customer_id']; ?>" 
                               class="text-green-600 hover:text-green-900 mr-3">
                                <i class="fas fa-history"></i> 
                            </a>
                            <a href="confirm_delete.php?id=<?php echo $customer['customer_id']; ?>" 
                               class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i> 
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Customer Modal -->
<div id="addCustomerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Add New Customer</h3>
            <form id="addCustomerForm" method="POST" action="add_customer.php">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" required 
                           class="w-full rounded-lg border-gray-300">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Information</label>
                    <input type="text" name="contact_info" required 
                           class="w-full rounded-lg border-gray-300">
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeAddCustomerModal()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Add Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 

<?php include_once '../includes/footer.php'; ?> 
