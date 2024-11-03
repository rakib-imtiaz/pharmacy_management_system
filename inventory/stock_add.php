<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

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
        INSERT INTO STOCK_ITEM (drug_id, supplier_id, quantity, expiry_date, unit_price, user_id) 
        VALUES (?, ?, ?, ?, ?, 1)
    "); // Assuming user_id 1 for now
    
    if ($stmt->execute([$drug_id, $supplier_id, $quantity, $expiry_date, $unit_price])) {
        $stock_id = $pdo->lastInsertId();
        log_audit($pdo, 1, 'INSERT', 'STOCK_ITEM', $stock_id);
        header("Location: inventory.php");
        exit();
    }
}
?>

<div class="container mx-auto">
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-6">Add New Stock</h2>
        
        <form method="POST">
            <div class="mb-4">
                <label class="block mb-2">Drug</label>
                <select name="drug_id" required class="w-full px-3 py-2 border rounded">
                    <option value="">Select Drug</option>
                    <?php foreach ($drugs as $drug): ?>
                        <option value="<?= $drug['drug_id'] ?>">
                            <?= htmlspecialchars($drug['name']) ?> (<?= htmlspecialchars($drug['dosage_form']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Supplier</label>
                <select name="supplier_id" required class="w-full px-3 py-2 border rounded">
                    <option value="">Select Supplier</option>
                    <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?= $supplier['supplier_id'] ?>">
                            <?= htmlspecialchars($supplier['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Quantity</label>
                <input type="number" name="quantity" required min="1"
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Unit Price ($)</label>
                <input type="number" name="unit_price" required step="0.01" min="0.01"
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Expiry Date</label>
                <input type="date" name="expiry_date" required
                       min="<?= date('Y-m-d') ?>"
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Add Stock
                </button>
                <a href="inventory.php" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 