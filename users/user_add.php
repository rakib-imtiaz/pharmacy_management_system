<?php
require_once '../includes/db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Insert the new user into the USER table
        $stmt = $pdo->prepare("INSERT INTO USER (username, password_hash, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $role]);
        
        // Get the generated user_id
        $user_id = $pdo->lastInsertId();

        // If the role is Supplier, insert an entry into the supplier table
        if ($role === 'Supplier') {
            $supplier_name = $_POST['supplier_name'] ?? 'Default Supplier Name'; // Adjust input as needed
            $stmt = $pdo->prepare("INSERT INTO supplier (user_id, name) VALUES (?, ?)");
            $stmt->execute([$user_id, $supplier_name]);
        }

        // If the role is Doctor, insert an entry into the doctor table
        if ($role === 'Doctor') {
            $doctor_name = $_POST['doctor_name'] ?? 'Default Doctor Name'; // Adjust input as needed
            $stmt = $pdo->prepare("INSERT INTO doctor (user_id, name) VALUES (?, ?)");
            $stmt->execute([$user_id, $doctor_name]);
        }

        // Commit transaction
        $pdo->commit();

        // Set success message and redirect
        $_SESSION['success'] = "User added successfully";
        header("Location: " . $base_url . "users/users.php");
        exit();

    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        $_SESSION['error'] = "Error adding user: " . $e->getMessage();
    }
}

require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Add New User</h1>
            <a href="<?php echo $base_url; ?>users/users.php" class="text-blue-500 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to Users
            </a>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="" method="POST">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input type="text" name="username" id="username" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" id="password" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-6">
                    <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                    <select name="role" id="role" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="Administrator">Administrator</option>
                        <option value="Cashier">Cashier</option>
                        <option value="Supplier">Supplier</option>
                        <option value="Doctor">Doctor</option>
                    </select>
                </div>

                <!-- Additional input for Supplier name if role is Supplier -->
                <div class="mb-4" id="supplier_name_field" style="display: none;">
                    <label for="supplier_name" class="block text-gray-700 text-sm font-bold mb-2">Supplier Name</label>
                    <input type="text" name="supplier_name" id="supplier_name"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           placeholder="Enter supplier name ">
                </div>

                <!-- Additional input for Doctor name if role is Doctor -->
                <div class="mb-4" id="doctor_name_field" style="display: none;">
                    <label for="doctor_name" class="block text-gray-700 text-sm font-bold mb-2">Doctor Name</label>
                    <input type="text" name="doctor_name" id="doctor_name"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           placeholder="Enter Doctor name ">
                </div>

                <div class="flex items-center justify-end">
                    <a href="users.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Add User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Show/hide Supplier and Doctor Name fields based on role selection
    document.getElementById('role').addEventListener('change', function() {
        document.getElementById('supplier_name_field').style.display = this.value === 'Supplier' ? 'block' : 'none';
        document.getElementById('doctor_name_field').style.display = this.value === 'Doctor' ? 'block' : 'none';
    });
</script>

<?php require_once '../includes/footer.php'; ?>
