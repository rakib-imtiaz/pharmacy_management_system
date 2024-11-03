<?php
require_once '../includes/db_connect.php';
session_start();
include_once '../includes/header.php';

$prescription_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch prescription details
$query = "
    SELECT 
        p.*,
        c.name as customer_name,
        d.name as doctor_name
    FROM PRESCRIPTION p
    JOIN CUSTOMER c ON p.customer_id = c.customer_id
    JOIN DOCTOR d ON p.doctor_id = d.doctor_id
    WHERE p.prescription_id = ? AND p.status != 'Filled'
";

$stmt = $pdo->prepare($query);
$stmt->execute([$prescription_id]);
$prescription = $stmt->fetch();

if (!$prescription) {
    $_SESSION['error'] = "Invalid prescription or already filled";
    header("Location: index.php");
    exit;
}

// Fetch prescription items
$items_query = "
    SELECT 
        pi.*,
        d.name as drug_name,
        d.dosage_form
    FROM PRESCRIPTION_ITEM pi
    JOIN DRUG d ON pi.drug_id = d.drug_id
    WHERE pi.prescription_id = ?
";

$stmt = $pdo->prepare($items_query);
$stmt->execute([$prescription_id]);
$prescription_items = $stmt->fetchAll();

// Fetch all customers for dropdown
$customers = $pdo->query("SELECT customer_id, name FROM CUSTOMER ORDER BY name")->fetchAll();

// Fetch all doctors for dropdown
$doctors = $pdo->query("SELECT doctor_id, name FROM DOCTOR ORDER BY name")->fetchAll();

// Fetch all drugs for dropdown
$drugs = $pdo->query("SELECT drug_id, name, dosage_form FROM DRUG ORDER BY name")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Update prescription details
        $update_prescription = $pdo->prepare("
            UPDATE PRESCRIPTION 
            SET 
                customer_id = ?,
                doctor_id = ?,
                prescription_date = ?
            WHERE prescription_id = ?
        ");
        
        $update_prescription->execute([
            $_POST['customer_id'],
            $_POST['doctor_id'],
            $_POST['prescription_date'],
            $prescription_id
        ]);

        // Delete existing items
        $delete_items = $pdo->prepare("
            DELETE FROM PRESCRIPTION_ITEM 
            WHERE prescription_id = ?
        ");
        $delete_items->execute([$prescription_id]);

        // Insert updated items
        $insert_item = $pdo->prepare("
            INSERT INTO PRESCRIPTION_ITEM 
            (prescription_id, drug_id, quantity, dosage_instructions) 
            VALUES (?, ?, ?, ?)
        ");

        foreach ($_POST['items'] as $item) {
            if (!empty($item['drug_id']) && !empty($item['quantity'])) {
                $insert_item->execute([
                    $prescription_id,
                    $item['drug_id'],
                    $item['quantity'],
                    $item['dosage_instructions']
                ]);
            }
        }

        $pdo->commit();
        $_SESSION['success'] = "Prescription updated successfully";
        header("Location: view.php?id=" . $prescription_id);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = $e->getMessage();
    }
}
?>

<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800">
            Edit Prescription #<?php echo $prescription_id; ?>
        </h2>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
        <!-- Basic Information -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-semibold mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Patient</label>
                    <select name="customer_id" required class="w-full rounded-lg border-gray-300">
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['customer_id']; ?>"
                                <?php echo ($customer['customer_id'] == $prescription['customer_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($customer['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Doctor</label>
                    <select name="doctor_id" required class="w-full rounded-lg border-gray-300">
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?php echo $doctor['doctor_id']; ?>"
                                <?php echo ($doctor['doctor_id'] == $prescription['doctor_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($doctor['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" name="prescription_date" 
                           value="<?php echo $prescription['prescription_date']; ?>"
                           required class="w-full rounded-lg border-gray-300">
                </div>
            </div>
        </div>

        <!-- Prescription Items -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-semibold mb-4">Prescription Items</h3>
            <div id="items-container">
                <?php foreach ($prescription_items as $index => $item): ?>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 item-row">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Medication</label>
                            <select name="items[<?php echo $index; ?>][drug_id]" required 
                                    class="w-full rounded-lg border-gray-300">
                                <?php foreach ($drugs as $drug): ?>
                                    <option value="<?php echo $drug['drug_id']; ?>"
                                        <?php echo ($drug['drug_id'] == $item['drug_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($drug['name'] . ' (' . $drug['dosage_form'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <input type="number" name="items[<?php echo $index; ?>][quantity]" 
                                   value="<?php echo $item['quantity']; ?>"
                                   required min="1" class="w-full rounded-lg border-gray-300">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                            <input type="text" name="items[<?php echo $index; ?>][dosage_instructions]" 
                                   value="<?php echo htmlspecialchars($item['dosage_instructions']); ?>"
                                   required class="w-full rounded-lg border-gray-300">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="add-item" 
                    class="mt-4 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Add Item
            </button>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="view.php?id=<?php echo $prescription_id; ?>" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                Cancel
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                Save Changes
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const index = container.children.length;
    const template = `
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 item-row">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Medication</label>
                <select name="items[${index}][drug_id]" required class="w-full rounded-lg border-gray-300">
                    <?php foreach ($drugs as $drug): ?>
                        <option value="<?php echo $drug['drug_id']; ?>">
                            <?php echo htmlspecialchars($drug['name'] . ' (' . $drug['dosage_form'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                <input type="number" name="items[${index}][quantity]" required min="1" 
                       class="w-full rounded-lg border-gray-300">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Instructions</label>
                <input type="text" name="items[${index}][dosage_instructions]" required 
                       class="w-full rounded-lg border-gray-300">
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', template);
});
</script>

<?php include_once '../includes/footer.php'; ?> 