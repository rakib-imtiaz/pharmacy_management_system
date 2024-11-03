<?php
require_once '../includes/db_connect.php';
session_start();

// Check if request is POST and has valid JSON content
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$response = ['success' => false, 'message' => ''];

try {
    if (isset($data['drug_id'])) {
        $drug_id = $data['drug_id'];
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // First, delete related records from STOCK_ITEM
        $stmt = $pdo->prepare("DELETE FROM STOCK_ITEM WHERE drug_id = ?");
        $stmt->execute([$drug_id]);
        
        // Then, delete from PRESCRIPTION_ITEM
        $stmt = $pdo->prepare("DELETE FROM PRESCRIPTION_ITEM WHERE drug_id = ?");
        $stmt->execute([$drug_id]);
        
        // Finally, delete the drug
        $stmt = $pdo->prepare("DELETE FROM DRUG WHERE drug_id = ?");
        $stmt->execute([$drug_id]);
        
        // Log the deletion
        $audit_stmt = $pdo->prepare("
            INSERT INTO AUDIT_LOG (user_id, timestamp, action, table_affected, record_id)
            VALUES (?, NOW(), 'DELETE', 'DRUG', ?)
        ");
        $audit_stmt->execute([$_SESSION['user_id'], $drug_id]);
        
        $pdo->commit();
        
        $response['success'] = true;
        $response['message'] = 'Drug deleted successfully';
        
    } else {
        $response['message'] = 'Drug ID not provided';
    }
} catch (PDOException $e) {
    $pdo->rollBack();
    $response['message'] = 'Database error: ' . $e->getMessage();
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response); 