<?php
require_once '../includes/db_connect.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['user_id'] ?? null;

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Check if user exists and get their role
    $stmt = $pdo->prepare("SELECT * FROM USER WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // Don't allow deletion of the last administrator
    if ($user['role'] === 'Administrator') {
        $admin_count_stmt = $pdo->query("SELECT COUNT(*) FROM USER WHERE role = 'Administrator'");
        $admin_count = $admin_count_stmt->fetchColumn();

        if ($admin_count <= 1) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Cannot delete the last administrator']);
            exit;
        }
    }

    // Delete related records in order
    try {
        // Delete from AUDIT_LOG
        $stmt = $pdo->prepare("DELETE FROM AUDIT_LOG WHERE user_id = ?");
        $stmt->execute([$userId]);

        // Delete from PAYMENT
        $stmt = $pdo->prepare("DELETE FROM PAYMENT WHERE user_id = ?");
        $stmt->execute([$userId]);

        // Delete from COUNTER_SALE_ITEM through COUNTER_SALE
        $stmt = $pdo->prepare("DELETE FROM COUNTER_SALE_ITEM WHERE sale_id IN (SELECT sale_id FROM COUNTER_SALE WHERE user_id = ?)");
        $stmt->execute([$userId]);

        // Delete from COUNTER_SALE
        $stmt = $pdo->prepare("DELETE FROM COUNTER_SALE WHERE user_id = ?");
        $stmt->execute([$userId]);

        // Delete from INVOICE_ITEM through INVOICE
        $stmt = $pdo->prepare("DELETE FROM INVOICE_ITEM WHERE invoice_id IN (SELECT invoice_id FROM INVOICE WHERE user_id = ?)");
        $stmt->execute([$userId]);

        // Delete from INVOICE
        $stmt = $pdo->prepare("DELETE FROM INVOICE WHERE user_id = ?");
        $stmt->execute([$userId]);

        // Delete from PRESCRIPTION
        $stmt = $pdo->prepare("DELETE FROM PRESCRIPTION WHERE user_id = ?");
        $stmt->execute([$userId]);

        // Delete from STOCK_ITEM
        $stmt = $pdo->prepare("DELETE FROM STOCK_ITEM WHERE user_id = ?");
        $stmt->execute([$userId]);

        // Finally delete the user
        $stmt = $pdo->prepare("DELETE FROM USER WHERE user_id = ?");
        $stmt->execute([$userId]);

        $pdo->commit();
        echo json_encode([
            'success' => true, 
            'message' => 'User deleted successfully',
            'redirect' => 'users.php'
        ]);

    } catch (PDOException $e) {
        $pdo->rollBack();
        // Log the specific error and return a more detailed message
        error_log("Error deleting user: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Error in user deletion process: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
