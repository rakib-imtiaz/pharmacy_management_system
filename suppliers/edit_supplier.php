<?php
require_once '../includes/db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: " . $base_url . "suppliers/suppliers.php");
    exit();
}

$supplier_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $contact_info = $_POST['contact_info'];
    $payment_terms = $_POST['payment_terms'];

    try {
        $stmt = $pdo->prepare("UPDATE SUPPLIER SET name = ?, contact_info = ?, payment_terms = ? WHERE supplier_id = ?");
        $stmt->execute([$name, $contact_info, $payment_terms, $supplier_id]);
        
        $_SESSION['success'] = "Supplier updated successfully";
        header("Location: " . $base_url . "suppliers/suppliers.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating supplier: " . $e->getMessage();
    }
}

// Fetch supplier details
$stmt = $pdo->prepare("SELECT * FROM SUPPLIER WHERE supplier_id = ?");
$stmt->execute([$supplier_id]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$supplier) {
    header("Location: " . $base_url . "suppliers/suppliers.php");
    exit();
}

require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit Supplier</h1>
            <a href="<?php echo $base_url; ?>suppliers/suppliers.php" class="text-blue-500 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to Suppliers
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="" method="POST">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" name="name" id="name" required
                           value="<?php echo htmlspecialchars($supplier['name']); ?>"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="contact_info" class="block text-gray-700 text-sm font-bold mb-2">Contact Info</label>
                    <input type="text" name="contact_info" id="contact_info" required
                           value="<?php echo htmlspecialchars($supplier['contact_info']); ?>"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-6">
                    <label for="payment_terms" class="block text-gray-700 text-sm font-bold mb-2">Payment Terms</label>
                    <input type="text" name="payment_terms" id="payment_terms" required
                           value="<?php echo htmlspecialchars($supplier['payment_terms']); ?>"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Update Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 