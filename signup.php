<?php
require_once 'includes/db_connect.php';
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $role = 'Cashier'; // Default role for new signups

        // Validate input
        if (empty($username) || empty($password) || empty($confirm_password)) {
            throw new Exception("All fields are required");
        }

        if ($password !== $confirm_password) {
            throw new Exception("Passwords do not match");
        }

        if (strlen($password) < 8) {
            throw new Exception("Password must be at least 8 characters long");
        }

        // Check if username already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM USER WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Username already exists");
        }

        // Create new user
        $stmt = $pdo->prepare("
            INSERT INTO USER (username, password_hash, role, last_login) 
            VALUES (?, ?, ?, CURRENT_TIMESTAMP)
        ");
        
        $stmt->execute([$username, $password, $role]);
        
        // Log the signup
        $user_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("
            INSERT INTO AUDIT_LOG (user_id, timestamp, action, table_affected, record_id)
            VALUES (?, CURRENT_TIMESTAMP, 'SIGNUP', 'USER', ?)
        ");
        $stmt->execute([$user_id, $user_id]);

        // Set success message
        $_SESSION['success'] = "Account created successfully. Please log in.";
        header("Location: login.php");
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - PharmaCare Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <!-- Signup Card -->
        <div class="bg-white rounded-xl shadow-lg p-8 animate__animated animate__fadeIn">
            <!-- Logo and Title -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <div class="bg-blue-500 p-3 rounded-full">
                        <i class="fas fa-clinic-medical text-3xl text-white"></i>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Create Account</h1>
                <p class="text-gray-600">PharmaCare Management System</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 animate__animated animate__shake" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <!-- Signup Form -->
            <form method="POST" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" id="username" name="username" required
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Choose a username">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="password" name="password" required
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Choose a password">
                    </div>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="confirm_password" name="confirm_password" required
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Confirm your password">
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300 flex items-center justify-center">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Account
                </button>
            </form>

            <!-- Back to Login -->
            <div class="mt-6 pt-6 border-t border-gray-200 text-center text-sm text-gray-600">
                <p>Already have an account? 
                    <a href="login.php" class="text-blue-500 hover:text-blue-600 font-semibold">
                        Login here
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
    // Add password strength indicator
    document.getElementById('password').addEventListener('input', function(e) {
        const password = e.target.value;
        const strength = {
            length: password.length >= 8,
            hasNumber: /\d/.test(password),
            hasUpper: /[A-Z]/.test(password),
            hasLower: /[a-z]/.test(password),
            hasSpecial: /[!@#$%^&*]/.test(password)
        };
        
        // You can add visual feedback based on password strength
        if (Object.values(strength).every(Boolean)) {
            e.target.classList.add('border-green-500');
            e.target.classList.remove('border-red-500');
        } else {
            e.target.classList.add('border-red-500');
            e.target.classList.remove('border-green-500');
        }
    });
    </script>
</body>
</html> 