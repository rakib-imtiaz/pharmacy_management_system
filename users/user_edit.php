<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitize_input($_POST['username']);
    $role = sanitize_input($_POST['role']);
    
    // Only update password if a new one is provided
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE USER SET username = ?, password_hash = ?, role = ? WHERE user_id = ?");
        $stmt->execute([$username, $password, $role, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE USER SET username = ?, role = ? WHERE user_id = ?");
        $stmt->execute([$username, $role, $id]);
    }
    
    header("Location: users.php");
    exit();
}

$user = $pdo->query("SELECT * FROM USER WHERE user_id = $id")->fetch();
?>

<div class="container mx-auto">
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-6">Edit User</h2>
        
        <form method="POST">
            <div class="mb-4">
                <label class="block mb-2">Username</label>
                <input type="text" name="username" required 
                       value="<?= htmlspecialchars($user['username']) ?>"
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">New Password (leave blank to keep current)</label>
                <input type="password" name="password"
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Role</label>
                <select name="role" required class="w-full px-3 py-2 border rounded">
                    <option value="Administrator" <?= $user['role'] == 'Administrator' ? 'selected' : '' ?>>Administrator</option>
                    <option value="Pharmacist" <?= $user['role'] == 'Pharmacist' ? 'selected' : '' ?>>Pharmacist</option>
                    <option value="Cashier" <?= $user['role'] == 'Cashier' ? 'selected' : '' ?>>Cashier</option>
                </select>
            </div>
            
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Update User
                </button>
                <a href="users.php" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 