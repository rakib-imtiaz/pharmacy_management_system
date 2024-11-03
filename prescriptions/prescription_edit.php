<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get customers, doctors and drugs for dropdowns
$customers = $pdo->query("SELECT * FROM CUSTOMER ORDER BY name")->fetchAll();
$doctors = $pdo->query("SELECT * FROM DOCTOR ORDER BY name")->fetchAll();
$drugs = $pdo->query("SELECT * FROM DRUG ORDER BY name")->fetchAll();

// Get prescription and its items
$prescription = $pdo->query("SELECT * FROM PRESCRIPTION WHERE prescription_id = $id")->fetch();
$items = $pdo->query("SELECT * FROM PRESCRIPTION_ITEM WHERE prescription_id = $id")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Update prescription
        $stmt = $pdo->prepare("
            UPDATE PRESCRIPTION 
            SET customer_id = ?, doctor_id = ?, status = ?
            WHERE prescription_id = ?
        ");
        $stmt->execute([
            $_POST['customer_id'],
            $_POST['doctor_id'],
            $_POST['status'],
            $id
        ]);
        
        // Delete existing items
        $pdo->exec("DELETE FROM PRESCRIPTION_ITEM WHERE prescription_id = $id");
        
        // Insert updated items
        $stmt = $pdo->prepare("
            INSERT INTO PRESCRIPTION_ITEM (prescription_id, drug_id, quantity, dosage_instructions) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($_POST['items'] as $item) {
            if (!empty($item['drug_id']) && !empty($item['quantity'])) {
                $stmt->execute([
                    $id,
                    $item['drug_id'],
                    $item['quantity'],
                    $item['instructions']
                ]);
            }
        }
        
        $pdo->commit();
        log_audit($pdo, 1, 'UPDATE', 'PRESCRIPTION', $id);
        header("Location: prescriptions.php");
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error updating prescription: " . $e->getMessage();
    }
}
?>

<div class="container mx-auto">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-6">Edit Prescription</h2>
        
        <form method="POST" id="prescriptionForm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block mb-2">Customer</label>
                    <select name="customer_id" required class="w-full px-3 py-2 border rounded">
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['customer_id'] ?>"
                                    <?= $customer['customer_id'] == $prescription['customer_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($customer['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block mb-2">Doctor</label>
                    <select name="doctor_id" required class="w-full px-3 py-2 border rounded">
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= $doctor['doctor_id'] ?>"
                                    <?= $doctor['doctor_id'] == $prescription['doctor_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($doctor['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block mb-2">Status</label>
                    <select name="status" required class="w-full px-3 py-2 border rounded">
                        <option value="Pending" <?= $prescription['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Filled" <?= $prescription['status'] == 'Filled' ? 'selected' : '' ?>>Filled</option>
                        <option value="Cancelled" <?= $prescription['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
            </div>
            
            <div id="prescriptionItems">
                <h3 class="text-lg font-semibold mb-4">Prescription Items</h3>
                <?php foreach ($items as $index => $item): ?>
                <div class="prescription-item mb-4 p-4 border rounded">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2">Drug</label>
                            <select name="items[<?= $index ?>][drug_id]" required class="w-full px-3 py-2 border rounded">
                                <?php foreach ($drugs as $drug): ?>
                                    <option value="<?= $drug['drug_id'] ?>"
                                            <?= $drug['drug_id'] == $item['drug_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($drug['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2">Quantity</label>
                            <input type="number" name="items[<?= $index ?>][quantity]" 
                                   value="<?= htmlspecialchars($item['quantity']) ?>"
                                   required min="1" class="w-full px-3 py-2 border rounded">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block mb-2">Dosage Instructions</label>
                        <textarea name="items[<?= $index ?>][instructions]" required
                                  class="w-full px-3 py-2 border rounded"><?= htmlspecialchars($item['dosage_instructions']) ?></textarea>
                    </div>
                    <?php if ($index > 0): ?>
                    <button type="button" onclick="this.parentElement.remove()" 
                            class="mt-2 text-red-500">Remove Item</button>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <button type="button" onclick="addItem()" 
                    class="mb-4 bg-gray-500 text-white px-4 py-2 rounded">
                Add Another Item
            </button>
            
            <div class="flex justify-between mt-6">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Update Prescription
                </button>
                <a href="prescriptions.php" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let itemCount = <?= count($items) ?>;

function addItem() {
    const itemsDiv = document.getElementById('prescriptionItems');
    const newItem = document.createElement('div');
    newItem.className = 'prescription-item mb-4 p-4 border rounded';
    newItem.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-2">Drug</label>
                <select name="items[${itemCount}][drug_id]" required class="w-full px-3 py-2 border rounded">
                    <option value="">Select Drug</option>
                    <?php foreach ($drugs as $drug): ?>
                        <option value="<?= $drug['drug_id'] ?>">
                            <?= htmlspecialchars($drug['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block mb-2">Quantity</label>
                <input type="number" name="items[${itemCount}][quantity]" required min="1"
                       class="w-full px-3 py-2 border rounded">
            </div>
        </div>
        <div class="mt-4">
            <label class="block mb-2">Dosage Instructions</label>
            <textarea name="items[${itemCount}][instructions]" required
                      class="w-full px-3 py-2 border rounded"></textarea>
        </div>
        <button type="button" onclick="this.parentElement.remove()" 
                class="mt-2 text-red-500">Remove Item</button>
    `;
    itemsDiv.appendChild(newItem);
    itemCount++;
}
</script>

<?php require_once '../includes/footer.php'; ?> 