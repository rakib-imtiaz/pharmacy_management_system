<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get prescription details
$prescription = $pdo->query("
    SELECT p.*, 
           c.name as customer_name,
           d.name as doctor_name
    FROM PRESCRIPTION p
    JOIN CUSTOMER c ON p.customer_id = c.customer_id
    JOIN DOCTOR d ON p.doctor_id = d.doctor_id
    WHERE p.prescription_id = $id
")->fetch();

// Get prescription items
$items = $pdo->query("
    SELECT pi.*, d.name as drug_name, d.dosage_form
    FROM PRESCRIPTION_ITEM pi
    JOIN DRUG d ON pi.drug_id = d.drug_id
    WHERE pi.prescription_id = $id
")->fetchAll();
?>

<div class="container mx-auto">
    <div class="bg-white p-6 rounded shadow">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Prescription Details</h2>
            <a href="prescriptions.php" class="text-blue-500">Back to List</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-gray-600">Customer</p>
                <p class="font-semibold"><?= htmlspecialchars($prescription['customer_name']) ?></p>
            </div>
            <div>
                <p class="text-gray-600">Doctor</p>
                <p class="font-semibold"><?= htmlspecialchars($prescription['doctor_name']) ?></p>
            </div>
            <div>
                <p class="text-gray-600">Date</p>
                <p class="font-semibold"><?= date('Y-m-d', strtotime($prescription['prescription_date'])) ?></p>
            </div>
            <div>
                <p class="text-gray-600">Status</p>
                <p class="font-semibold"><?= htmlspecialchars($prescription['status']) ?></p>
            </div>
        </div>
        
        <h3 class="text-xl font-semibold mb-4">Prescribed Items</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left">Drug</th>
                        <th class="px-6 py-3 text-left">Quantity</th>
                        <th class="px-6 py-3 text-left">Dosage Instructions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr class="border-b">
                        <td class="px-6 py-4">
                            <?= htmlspecialchars($item['drug_name']) ?>
                            <span class="text-sm text-gray-500 block">
                                <?= htmlspecialchars($item['dosage_form']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4"><?= htmlspecialchars($item['quantity']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($item['dosage_instructions']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 