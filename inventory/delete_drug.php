<?php
require_once '../includes/db_connect.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $drug_id = $data['drug_id'];

    try {
        $pdo->beginTransaction();

        // 1. First get all stock_item_ids for this drug
        $stock_items_stmt = $pdo->prepare("SELECT stock_item_id FROM STOCK_ITEM WHERE drug_id = ?");
        $stock_items_stmt->execute([$drug_id]);
        $stock_items = $stock_items_stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($stock_items as $stock_item_id) {
            // 2. Delete from COUNTER_SALE_ITEM
            $delete_sale_items = $pdo->prepare("DELETE FROM COUNTER_SALE_ITEM WHERE stock_item_id = ?");
            $delete_sale_items->execute([$stock_item_id]);

            // 3. Delete from INVOICE_ITEM
            $delete_invoice_items = $pdo->prepare("DELETE FROM INVOICE_ITEM WHERE stock_item_id = ?");
            $delete_invoice_items->execute([$stock_item_id]);
        }

        // 4. Delete from STOCK_ITEM
        $delete_stock = $pdo->prepare("DELETE FROM STOCK_ITEM WHERE drug_id = ?");
        $delete_stock->execute([$drug_id]);

        // 5. Delete from PRESCRIPTION_ITEM
        $delete_prescription_items = $pdo->prepare("DELETE FROM PRESCRIPTION_ITEM WHERE drug_id = ?");
        $delete_prescription_items->execute([$drug_id]);

        // 6. Finally delete the drug
        $delete_drug = $pdo->prepare("DELETE FROM DRUG WHERE drug_id = ?");
        $delete_drug->execute([$drug_id]);

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Drug deleted successfully']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'message' => 'Cannot delete this drug. It may have related records in sales or prescriptions.'
        ]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Invalid request method']);
