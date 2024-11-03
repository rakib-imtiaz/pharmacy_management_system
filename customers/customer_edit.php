<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize_input($_POST['name']);
    $contact_info = sanitize_input($_POST['contact_info']);
    
    $stmt = $pdo->prepare("UPDATE CUSTOMER SET name = ?, contact_info = ? WHERE customer_id = ?");
    if ($stmt->execute([$name, $contact_info, $id])) {
        header("Location: customers.php");
        exit();
    }
}

$customer = $pdo->query("SELECT * FROM CUSTOMER WHERE customer_id = $id")->fetch();
?>

<div class="container mx-auto">
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-6">Edit Customer</h2>
        
        <form method="POST">
            <div class="mb-4">
                <label class="block mb-2">Name</label>
                <input type="text" name="name" required 
                       value="<?= htmlspecialchars($customer['name']) ?>"
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Contact Info</label>
                <input type="text" name="contact_info" required 
                       value="<?= htmlspecialchars($customer['contact_info']) ?>"
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Update Customer
                </button>
                <a href="customers.php" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 