<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get drugs and suppliers for dropdowns
$drugs = $pdo->query("SELECT * FROM DRUG ORDER BY name")->fetchAll();
$suppliers = $pdo->query("SELECT * FROM SUPPLIER ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $drug_id = (int)$_POST['drug_id'];
    $supplier_id = (int)$_POST['supplier_id'];
    $quantity = (int)$_POST['quantity'];
    $unit_price = (float)$_POST['unit_price'];
    $expiry_date = $_POST['expiry_date'];
    
    $stmt = $pdo->prepare("
        UPDATE STOCK_ITEM 
        SET drug_id = ?, supplier_id = ?, quantity = ?, expiry_date = ?, unit_price = ? 
        WHERE stock_item_id = ?
    ");
    
    if ($stmt->execute([$drug_id, $supplier_id, $quantity, $expiry_date, $unit_price, $id])) {
        log_audit($pdo, 1, 'UPDATE', 'STOCK_ITEM', $id);
        header("Location: inventory.php");
        exit();
    }
}

$stock = $pdo->query("SELECT * FROM STOCK_ITEM WHERE stock_item_id = $id")->fetch();
?>

<div class="container mx-auto">
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-6">Edit Stock Item</h2>
        
        <form method="POST">
            <div class="mb-4">
                <label class="block mb-2">Drug</label>
                <select name="drug_id" required class="w-full px-3 py-2 border rounded">
                    <?php foreach ($drugs as $drug): ?>
                        <option value="<?= $drug['drug_id'] ?>" 
                                <?= $drug['drug_id'] == $stock['drug_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($drug['name']) ?> (<?= htmlspecialchars($drug['dosage_form']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Supplier</label>
                <select name="supplier_id" required class="w-full px-3 py-2 border rounded">
                    <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?= $supplier['supplier_id'] ?>"
                                <?= $supplier['supplier_id'] == $stock['supplier_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($supplier['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Quantity</label>
                <input type="number" name="quantity" required min="1"
                       value="<?= htmlspecialchars($stock['quantity']) ?>"
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Unit Price ($)</label>
                <input type="number" name="unit_price" required step="0.01" min="0.01"
                       value="<?= htmlspecialchars($stock['unit_price']) ?>"
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Expiry Date</label>
                <input type="date" name="expiry_date" required
                       value="<?= htmlspecialchars($stock['expiry_date']) ?>"
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Update Stock
                </button>
                <a href="inventory.php" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 