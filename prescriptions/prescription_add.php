<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

// Get customers and doctors for dropdowns
$customers = $pdo->query("SELECT * FROM CUSTOMER ORDER BY name")->fetchAll();
$doctors = $pdo->query("SELECT * FROM DOCTOR ORDER BY name")->fetchAll();
$drugs = $pdo->query("SELECT * FROM DRUG ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Insert prescription
        $stmt = $pdo->prepare("
            INSERT INTO PRESCRIPTION (customer_id, doctor_id, prescription_date, status, user_id) 
            VALUES (?, ?, CURDATE(), 'Pending', 1)
        ");
        $stmt->execute([$_POST['customer_id'], $_POST['doctor_id']]);
        $prescription_id = $pdo->lastInsertId();
        
        // Insert prescription items
        $stmt = $pdo->prepare("
            INSERT INTO PRESCRIPTION_ITEM (prescription_id, drug_id, quantity, dosage_instructions) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($_POST['items'] as $item) {
            if (!empty($item['drug_id']) && !empty($item['quantity'])) {
                $stmt->execute([
                    $prescription_id,
                    $item['drug_id'],
                    $item['quantity'],
                    $item['instructions']
                ]);
            }
        }
        
        $pdo->commit();
        log_audit($pdo, 1, 'INSERT', 'PRESCRIPTION', $prescription_id);
        header("Location: prescriptions.php");
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error creating prescription: " . $e->getMessage();
    }
}
?>

<div class="container mx-auto">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-6">New Prescription</h2>
        
        <form method="POST" id="prescriptionForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-2">Customer</label>
                    <select name="customer_id" required class="w-full px-3 py-2 border rounded">
                        <option value="">Select Customer</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['customer_id'] ?>">
                                <?= htmlspecialchars($customer['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block mb-2">Doctor</label>
                    <select name="doctor_id" required class="w-full px-3 py-2 border rounded">
                        <option value="">Select Doctor</option>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= $doctor['doctor_id'] ?>">
                                <?= htmlspecialchars($doctor['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div id="prescriptionItems">
                <h3 class="text-lg font-semibold mb-4">Prescription Items</h3>
                <div class="prescription-item mb-4 p-4 border rounded">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2">Drug</label>
                            <select name="items[0][drug_id]" required class="w-full px-3 py-2 border rounded">
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
                            <input type="number" name="items[0][quantity]" required min="1"
                                   class="w-full px-3 py-2 border rounded">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="block mb-2">Dosage Instructions</label>
                        <textarea name="items[0][instructions]" required
                                  class="w-full px-3 py-2 border rounded"></textarea>
                    </div>
                </div>
            </div>
            
            <button type="button" onclick="addItem()" 
                    class="mb-4 bg-gray-500 text-white px-4 py-2 rounded">
                Add Another Item
            </button>
            
            <div class="flex justify-between mt-6">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Save Prescription
                </button>
                <a href="prescriptions.php" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let itemCount = 1;

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