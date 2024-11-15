<?php
require_once '../includes/db_connect.php';
session_start();

// Debugging: Check if supplier_id is in session
// echo '<pre>';
// print_r($_SESSION); // Should show 'supplier_id' for suppliers
// echo '</pre>';

// Redirect if not logged in or invalid role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Administrator', 'Supplier'])) {
    header("Location: " . $base_url . "login.php");
    exit();
}

// Check if the logged-in user is an admin
$is_admin = ($_SESSION['role'] === 'Administrator');

// Set supplier_id for logged-in supplier
if (!$is_admin) {
    // Ensure supplier_id is available in session for suppliers
    if (empty($_SESSION['supplier_id'])) {
        $_SESSION['error'] = "Supplier ID not found in session.";
        header("Location: " . $base_url . "login.php");
        exit();
    }
    $supplier_id = $_SESSION['supplier_id']; // Supplier's ID from the session
} else {
    // For admin: Supplier selection is required
    $supplier_id = null;
}

// Fetch suppliers for dropdown (only for admin)
$suppliers = [];
if ($is_admin) {
    $suppliers = $pdo->query("SELECT supplier_id, name FROM SUPPLIER ORDER BY name")->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // For admin, ensure supplier_id is set
    if ($is_admin && isset($_POST['supplier_id'])) {
        $supplier_id = $_POST['supplier_id'];  // Get supplier_id from the form if admin
    }

    // Ensure the supplier_id is set before continuing
    if (empty($supplier_id)) {
        $_SESSION['error'] = "Supplier is required.";
        header("Location: " . $base_url . "invoices/add_invoice.php");
        exit();
    }

    // Sanitize and collect POST data
    $invoice_date = $_POST['invoice_date'];
    $total_amount = $_POST['total_amount'];
    $status = isset($_POST['status']) ? $_POST['status'] : 'Pending'; // Default to "Pending" if not set

    // Ensure invoice_date and total_amount are set
    if (empty($invoice_date) || empty($total_amount)) {
        $_SESSION['error'] = "Invoice date and total amount are required.";
        header("Location: " . $base_url . "invoices/add_invoice.php");
        exit();
    }

    try {
        // Prepare SQL to insert a new invoice
        $stmt = $pdo->prepare("
            INSERT INTO INVOICE (supplier_id, invoice_date, total_amount, status, user_id) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        // Execute the statement
        $stmt->execute([
            $supplier_id,          // Supplier ID from session or POST (admin selects)
            $invoice_date,         // Invoice date
            $total_amount,         // Total amount of the invoice
            $status,               // Status of the invoice
            $_SESSION['user_id']   // User ID of the person creating the invoice
        ]);
        
        // On success, redirect to invoices page
        $_SESSION['success'] = "Invoice added successfully";
        header("Location: " . $base_url . "invoices/invoices.php");
        exit();
    } catch (PDOException $e) {
        // Handle errors
        $_SESSION['error'] = "Error adding invoice: " . $e->getMessage();
    }
}

require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Add New Invoice</h1>
            <a href="<?php echo $base_url; ?>invoices/invoices.php" class="text-blue-500 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to Invoices
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="" method="POST">
                <!-- Admin can select a supplier -->
                <?php if ($is_admin): ?>
                    <div class="mb-4">
                        <label for="supplier_id" class="block text-gray-700 text-sm font-bold mb-2">Supplier *</label>
                        <select name="supplier_id" id="supplier_id" required
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Select Supplier</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?php echo $supplier['supplier_id']; ?>"
                                    <?php echo isset($supplier_id) && $supplier_id == $supplier['supplier_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($supplier['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- Invoice date input -->
                <div class="mb-4">
                    <label for="invoice_date" class="block text-gray-700 text-sm font-bold mb-2">Invoice Date *</label>
                    <input type="date" name="invoice_date" id="invoice_date" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Total amount input -->
                <div class="mb-4">
                    <label for="total_amount" class="block text-gray-700 text-sm font-bold mb-2">Total Amount (৳) *</label>
                    <input type="number" step="0.01" name="total_amount" id="total_amount" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <!-- Status dropdown -->
                <div class="mb-6">
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status *</label>
                    <select name="status" id="status" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="Pending">Pending</option>
                        <option value="Paid">Paid</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- Submit button -->
                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Add Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 
