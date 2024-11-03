<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize_input($_POST['name']);
    $contact_info = sanitize_input($_POST['contact_info']);
    
    $stmt = $pdo->prepare("INSERT INTO CUSTOMER (name, contact_info, registration_date) VALUES (?, ?, CURDATE())");
    if ($stmt->execute([$name, $contact_info])) {
        header("Location: customers.php");
        exit();
    }
}
?>

<div class="container mx-auto">
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-6">Add New Customer</h2>
        
        <form method="POST">
            <div class="mb-4">
                <label class="block mb-2">Name</label>
                <input type="text" name="name" required 
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Contact Info</label>
                <input type="text" name="contact_info" required 
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Save Customer
                </button>
                <a href="customers.php" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 