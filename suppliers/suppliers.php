<?php
require_once '../includes/db_connect.php';
session_start();

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header("Location: " . $base_url . "login.php");
    exit();
}

// Handle supplier deletion
if (isset($_POST['delete_supplier'])) {
    $supplier_id = $_POST['supplier_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM SUPPLIER WHERE supplier_id = ?");
        $stmt->execute([$supplier_id]);
        $_SESSION['success'] = "Supplier deleted successfully";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting supplier: " . $e->getMessage();
    }
    header("Location: " . $base_url . "suppliers/suppliers.php");
    exit();
}

require_once '../includes/header.php';

// Fetch all suppliers
$stmt = $pdo->query("SELECT * FROM SUPPLIER ORDER BY name");
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Supplier Management</h1>
        <a href="<?php echo $base_url; ?>suppliers/add_supplier.php" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
            <i class="fas fa-plus mr-2"></i>Add Supplier
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Terms</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($suppliers as $supplier): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($supplier['name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($supplier['contact_info']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($supplier['payment_terms']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex space-x-2">
                                <a href="<?php echo $base_url; ?>suppliers/edit_supplier.php?id=<?php echo $supplier['supplier_id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="" method="POST" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this supplier?');">
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

<?php require_once '../includes/footer.php'; ?>

