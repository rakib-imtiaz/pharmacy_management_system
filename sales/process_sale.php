<?php
require_once '../includes/db_connect.php';
session_start();

// Verify user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please log in to process sales";
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method";
    header("Location: sales.php");
    exit;
}

try {
    // Start transaction
    $pdo->beginTransaction();

    // Validate basic sale information
    if (empty($_POST['items']) || !is_array($_POST['items'])) {
        throw new Exception("No items in sale");
    }

    // Insert sale record
    $create_sale = $pdo->prepare("
        INSERT INTO COUNTER_SALE 
        (customer_id, sale_date, total_amount, user_id) 
        VALUES (?, CURRENT_DATE, ?, ?)
    ");

    $customer_id = !empty($_POST['customer_id']) ? $_POST['customer_id'] : null;
    $total_amount = 0;

    // Calculate total and validate stock
    foreach ($_POST['items'] as $item) {
        if (empty($item['stock_item_id']) || empty($item['quantity']) || empty($item['unit_price'])) {
            throw new Exception("Invalid item data");
        }

        // Verify stock availability
        $check_stock = $pdo->prepare("
            SELECT quantity, expiry_date 
            FROM STOCK_ITEM 
            WHERE stock_item_id = ?
        ");
        $check_stock->execute([$item['stock_item_id']]);
        $stock = $check_stock->fetch();

        if (!$stock) {
            throw new Exception("Item with ID " . $item['stock_item_id'] . " not found in stock.");
        }

        // Check if requested quantity is available
        if ($stock['quantity'] < $item['quantity']) {
            throw new Exception("Insufficient stock for item ID " . $item['stock_item_id'] . ": Requested " . $item['quantity'] . ", Available " . $stock['quantity']);
        }

        // Check if item has expired
        if (strtotime($stock['expiry_date']) < strtotime('today')) {
            throw new Exception("Item with ID " . $item['stock_item_id'] . " has expired");
        }

        $total_amount += $item['quantity'] * $item['unit_price'];
    }

    // Create the sale
    $create_sale->execute([
        $customer_id,
        $total_amount,
        $_SESSION['user_id']
    ]);

    $sale_id = $pdo->lastInsertId();

    // Insert sale items and update stock
    $create_sale_item = $pdo->prepare("
        INSERT INTO COUNTER_SALE_ITEM 
        (sale_id, stock_item_id, quantity, unit_price) 
        VALUES (?, ?, ?, ?)
    ");

    $update_stock = $pdo->prepare("
        UPDATE STOCK_ITEM 
        SET quantity = quantity - ? 
        WHERE stock_item_id = ?
    ");

    foreach ($_POST['items'] as $item) {
        // Create sale item
        $create_sale_item->execute([
            $sale_id,
            $item['stock_item_id'],
            $item['quantity'],
            $item['unit_price']
        ]);

        // Update stock quantity
        $update_stock->execute([
            $item['quantity'],
            $item['stock_item_id']
        ]);
    }

    // Create audit log entry
    $create_audit = $pdo->prepare("
        INSERT INTO AUDIT_LOG 
        (user_id, timestamp, action, table_affected, record_id) 
        VALUES (?, CURRENT_TIMESTAMP, ?, ?, ?)
    ");

    $create_audit->execute([
        $_SESSION['user_id'],
        'CREATE',
        'COUNTER_SALE',
        $sale_id
    ]);

    // Commit transaction
    $pdo->commit();

    // Success response
    $_SESSION['success'] = "Sale processed successfully";
    header("Location: view_sale.php?id=" . $sale_id);
    exit;

} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();

    // Log the error
    error_log("Sale processing error: " . $e->getMessage());
    
    // Error response
    $_SESSION['error'] = "Error processing sale: " . $e->getMessage();
    header("Location: sales.php");
    exit;
}
?>
