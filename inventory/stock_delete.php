<?php
require_once '../includes/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Check if stock item is used in any sales or invoices
    $usage_count = $pdo->query("
        SELECT 
            (SELECT COUNT(*) FROM COUNTER_SALE_ITEM WHERE stock_item_id = $id) +
            (SELECT COUNT(*) FROM INVOICE_ITEM WHERE stock_item_id = $id) as total
    ")->fetchColumn();
    
    if ($usage_count == 0) {
        $stmt = $pdo->prepare("DELETE FROM STOCK_ITEM WHERE stock_item_id = ?");
        if ($stmt->execute([$id])) {
            log_audit($pdo, 1, 'DELETE', 'STOCK_ITEM', $id);
        }
    } else {
        session_start();
        $_SESSION['error_message'] = "Cannot delete stock item: It is referenced in sales or invoices.";
    }
}

header("Location: inventory.php");
exit();
?> 