<?php
require_once '../includes/db_connect.php';
session_start();

// Check if the user role is set in the session
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$supplier_id = $_SESSION['supplier_id'] ?? null; // For suppliers, store their supplier ID
?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<?php include_once '../includes/header.php'; ?>

<?php
if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Success!',
                text: '<?php echo htmlspecialchars($message); ?>',
                icon: 'success',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        });
    </script>
    <?php
}
?>

<div class="relative min-h-screen">
    <div class="absolute inset-0 z-0 pointer-events-none">
        <img src="../assets/images/image2.png" class="w-full h-full object-cover opacity-15" alt="Background">
    </div>

    <div class="container mx-auto px-6 py-8 relative z-10">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 animate__animated animate__fadeIn">
                Inventory Management
                <span class="block text-lg font-normal text-gray-600 mt-2">Manage your pharmacy stock efficiently</span>
            </h2>
            <button onclick="window.location.href='add_drug.php'" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition duration-300 flex items-center">
                <i class="fas fa-plus mr-2"></i> Add New Drug
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <?php
            // Adjust total drugs query for admin vs. supplier
            $stats = [
                [
                    'query' => $is_admin ? "SELECT COUNT(*) FROM DRUG" : "SELECT COUNT(*) FROM DRUG WHERE supplier_id = $supplier_id",
                    'title' => 'Total Drugs',
                    'icon' => 'fas fa-pills',
                    'color' => 'blue'
                ],
                [
                    'query' => $is_admin ? "SELECT COUNT(*) FROM STOCK_ITEM WHERE quantity < 100" : "SELECT COUNT(*) FROM STOCK_ITEM WHERE quantity < 100 AND supplier_id = $supplier_id",
                    'title' => 'Low Stock Items',
                    'icon' => 'fas fa-exclamation-triangle',
                    'color' => 'yellow'
                ],
                [
                    'query' => "SELECT COUNT(*) FROM DRUG_CATEGORY",
                    'title' => 'Categories',
                    'icon' => 'fas fa-tags',
                    'color' => 'green'
                ],
                [
                    'query' => $is_admin ? "SELECT COUNT(*) FROM STOCK_ITEM WHERE quantity = 0" : "SELECT COUNT(*) FROM STOCK_ITEM WHERE quantity = 0 AND supplier_id = $supplier_id",
                    'title' => 'Out of Stock',
                    'icon' => 'fas fa-box-open',
                    'color' => 'red'
                ]
            ];

            foreach ($stats as $stat) {
                $stmt = $pdo->query($stat['query']);
                $count = $stmt->fetchColumn();
            ?>
                <div class="bg-white rounded-xl shadow-lg p-6 animate__animated animate__fadeInUp">
                    <div class="flex items-center">
                        <div class="p-4 rounded-full bg-<?php echo $stat['color']; ?>-500 bg-opacity-75">
                            <i class="<?php echo $stat['icon']; ?> fa-2x text-white"></i>
                        </div>
                        <div class="ml-6">
                            <h4 class="text-3xl font-bold text-gray-700"><?php echo number_format($count); ?></h4>
                            <div class="text-gray-500 mt-1"><?php echo $stat['title']; ?></div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" id="search" placeholder="Search drugs..." 
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex gap-4">
                    <select id="category" class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        <?php
                        $categories = $pdo->query("SELECT * FROM DRUG_CATEGORY ORDER BY name");
                        while ($category = $categories->fetch()) {
                            echo "<option value='{$category['category_id']}'>{$category['name']}</option>";
                        }
                        ?>
                    </select>
                    <select id="stock" class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Stock Levels</option>
                        <option value="low">Low Stock</option>
                        <option value="out">Out of Stock</option>
                        <option value="available">In Stock</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Drug Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    // Adjust query for admin vs supplier
                    $query = "SELECT d.*, dc.name as category_name, si.quantity, s.name as supplier_name 
                             FROM DRUG d 
                             LEFT JOIN DRUG_CATEGORY dc ON d.category_id = dc.category_id 
                             LEFT JOIN STOCK_ITEM si ON d.drug_id = si.drug_id 
                             LEFT JOIN SUPPLIER s ON d.supplier_id = s.supplier_id ";
                    $query .= $is_admin ? "" : "WHERE d.supplier_id = $supplier_id ";
                    $query .= "ORDER BY d.name";
                    
                    $drugs = $pdo->query($query);
                    
                    while ($drug = $drugs->fetch()) {
                        $stockStatus = '';
                        $statusColor = '';
                        
                        if ($drug['quantity'] <= 0) {
                            $stockStatus = 'Out of Stock';
                            $statusColor = 'red';
                        } elseif ($drug['quantity'] < 100) {
                            $stockStatus = 'Low Stock';
                            $statusColor = 'yellow';
                        } else {
                            $stockStatus = 'In Stock';
                            $statusColor = 'green';
                        }
                    ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($drug['name']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($drug['dosage_form']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($drug['supplier_name']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($drug['category_name']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo number_format($drug['quantity']); ?> units</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-<?php echo $statusColor; ?>-100 text-<?php echo $statusColor; ?>-800">
                                    <?php echo $stockStatus; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <?php if ($is_admin || $drug['supplier_id'] == $supplier_id) { ?>
                                    <button class="text-blue-600 hover:text-blue-900 mr-3" onclick="window.location.href='edit_drug.php?id=<?php echo $drug['drug_id']; ?>'">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                <?php } ?>
                                <?php if ($is_admin) { ?>
                                    <button class="text-red-600 hover:text-red-900" onclick="deleteDrug(<?php echo $drug['drug_id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
let currentDrugId = null;

function deleteDrug(drugId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we process your request',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('delete_drug.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ drug_id: drugId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Drug has been deleted successfully.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to delete drug');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to delete the drug.',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            });
        }
    });
}

document.getElementById('search').addEventListener('input', filterTable);
document.getElementById('category').addEventListener('change', filterTable);
document.getElementById('stock').addEventListener('change', filterTable);

function filterTable() {
    // Filtering logic
}
</script>

<?php include_once '../includes/footer.php'; ?>
