<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_input($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = sanitize_input($_POST['role']);
    
    $stmt = $pdo->prepare("INSERT INTO USER (username, password_hash, role) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $password, $role])) {
        header("Location: users.php");
        exit();
    }
}
?>

<div class="container mx-auto">
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-6">Add New User</h2>
        
        <form method="POST">
            <div class="mb-4">
                <label class="block mb-2">Username</label>
                <input type="text" name="username" required 
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Password</label>
                <input type="password" name="password" required 
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Role</label>
                <select name="role" required class="w-full px-3 py-2 border rounded">
                    <option value="Administrator">Administrator</option>
                    <option value="Pharmacist">Pharmacist</option>
                    <option value="Cashier">Cashier</option>
                </select>
            </div>
            
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Save User
                </button>
                <a href="users.php" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 