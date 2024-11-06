<?php
require_once '../includes/db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: " . $base_url . "users/users.php");
    exit();
}

$user_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    
    try {
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE USER SET username = ?, password_hash = ?, role = ? WHERE user_id = ?");
            $stmt->execute([$username, $password, $role, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE USER SET username = ?, role = ? WHERE user_id = ?");
            $stmt->execute([$username, $role, $user_id]);
        }
        
        $_SESSION['success'] = "User updated successfully";
        header("Location: " . $base_url . "users/users.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating user: " . $e->getMessage();
    }
}

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM USER WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: " . $base_url . "users/users.php");
    exit();
}

require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Edit User</h1>
            <a href="<?php echo $base_url; ?>users/users.php" class="text-blue-500 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to Users
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="" method="POST">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input type="text" name="username" id="username" required
                           value="<?php echo htmlspecialchars($user['username']); ?>"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">New Password (leave blank to keep current)</label>
                    <input type="password" name="password" id="password"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-6">
                    <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                    <select name="role" id="role" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="Administrator" <?php echo $user['role'] === 'Administrator' ? 'selected' : ''; ?>>Administrator</option>
                        <option value="Cashier" <?php echo $user['role'] === 'Cashier' ? 'selected' : ''; ?>>Cashier</option>
                        <option value="Pharmacist" <?php echo $user['role'] === 'Pharmacist' ? 'selected' : ''; ?>>Pharmacist</option>
                    </select>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
