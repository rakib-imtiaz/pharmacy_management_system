<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verify login status
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $base_url . "login.php");
    exit;
}

$is_admin = ($_SESSION['role'] === 'Administrator');
$is_supplier = ($_SESSION['role'] === 'Supplier');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaCare - Pharmacy Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .nav-link {
            @apply flex items-center space-x-2 px-4 py-2 text-white hover:bg-blue-700 rounded-lg transition-colors;
        }
        .nav-icon {
            @apply text-lg text-white;
        }
    </style>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center h-16">
                <!-- Logo Section -->
                <div class="flex-shrink-0">
                    <a href="<?php echo $base_url; ?>" class="flex items-center space-x-2">
                        <i class="fas fa-clinic-medical text-2xl text-white"></i>
                        <span class="text-white text-lg font-semibold">PharmaCare</span>
                    </a>
                </div>

                <!-- Navigation Links - Centered -->
                <div class="flex-grow flex justify-center space-x-4">
                    <a href="<?php echo $base_url; ?>" class="nav-link text-white">
                        <i class="fas fa-home nav-icon text-white"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <?php if (!$is_supplier): // Exclude sales, prescriptions, and customers for suppliers ?>
                    <a href="<?php echo $base_url; ?>sales/sales.php" class="nav-link text-white">
                        <i class="fas fa-cash-register nav-icon text-white"></i>
                        <span>Sales</span>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>prescriptions/prescriptions.php" class="nav-link text-white">
                        <i class="fas fa-file-prescription nav-icon text-white"></i>
                        <span>Prescriptions</span>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>customers/customers.php" class="nav-link text-white">
                        <i class="fas fa-users nav-icon text-white"></i>
                        <span>Customers</span>
                    </a>
                    <?php endif; ?>

                    <?php if ($is_admin): ?>
                    <a href="<?php echo $base_url; ?>inventory/inventory.php" class="nav-link text-white">
                        <i class="fas fa-boxes nav-icon text-white"></i>
                        <span>Inventory</span>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>suppliers/suppliers.php" class="nav-link text-white">
                        <i class="fas fa-truck nav-icon text-white"></i>
                        <span>Suppliers</span>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>doctors/doctors.php" class="nav-link text-white">
                        <i class="fas fa-user-md nav-icon text-white"></i>
                        <span>Doctors</span>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>users/users.php" class="nav-link text-white">
                        <i class="fas fa-users-cog nav-icon text-white"></i>
                        <span>Users</span>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>invoices/invoices.php" class="nav-link text-white">
                        <i class="fas fa-file-invoice nav-icon text-white"></i>
                        <span>Invoices</span>
                    </a>
                    <?php elseif ($is_supplier): ?>
                    <a href="<?php echo $base_url; ?>inventory/inventory.php" class="nav-link text-white">
                        <i class="fas fa-truck nav-icon text-white"></i>
                        <span>Update Stock</span>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>invoices/invoices.php" class="nav-link text-white">
                        <i class="fas fa-file-invoice nav-icon text-white"></i>
                        <span>Invoices</span>
                    </a>
                    <?php endif; ?>
                </div>

                <!-- User Profile Dropdown -->
                <div class="flex-shrink-0 relative">
                    <button class="flex items-center space-x-2 text-white hover:bg-blue-700 rounded-lg px-3 py-2 transition-colors">
                        <i class="fas fa-user-circle text-xl text-white"></i>
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </button>
                    <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-2 z-10">
                        <a href="<?php echo $base_url; ?>profile.php" class="block px-4 py-2 text-gray-800 hover:bg-blue-500 hover:text-white">
                            <i class="fas fa-user-cog mr-2"></i>Profile
                        </a>
                        <a href="<?php echo $base_url; ?>logout.php" class="block px-4 py-2 text-gray-800 hover:bg-blue-500 hover:text-white">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <script>
    // Toggle dropdown menu
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownButton = document.querySelector('.relative button');
        const dropdownMenu = document.querySelector('.relative .hidden');
        
        dropdownButton.addEventListener('click', function() {
            dropdownMenu.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    });
    </script>
</body>
</html>
