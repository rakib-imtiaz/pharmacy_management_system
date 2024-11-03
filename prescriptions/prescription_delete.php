<?php
require_once '../includes/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        $pdo->beginTransaction();
        
        // Delete prescription items first
        $pdo->exec("DELETE FROM PRESCRIPTION_ITEM WHERE prescription_id = $id");
        
        // Then delete the prescription
        $stmt = $pdo->prepare("DELETE FROM PRESCRIPTION WHERE prescription_id = ?");
        $stmt->execute([$id]);
        
        log_audit($pdo, 1, 'DELETE', 'PRESCRIPTION', $id);
        
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        session_start();
        $_SESSION['error_message'] = "Error deleting prescription: " . $e->getMessage();
    }
}

header("Location: prescriptions.php");
exit();
?> 