<?php
require_once '../includes/db_connect.php';
session_start();
include_once '../includes/header.php';

// Get prescription ID from URL
$prescription_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch prescription details
$query = "
    SELECT 
        p.prescription_id,
        p.prescription_date,
        p.status,
        c.name as customer_name,
        c.contact_info as customer_contact,
        d.name as doctor_name,
        d.specialization as doctor_specialization,
        d.contact_info as doctor_contact,
        u.username as created_by
    FROM PRESCRIPTION p
    JOIN CUSTOMER c ON p.customer_id = c.customer_id
    JOIN DOCTOR d ON p.doctor_id = d.doctor_id
    JOIN USER u ON p.user_id = u.user_id
    WHERE p.prescription_id = ?
";

$stmt = $pdo->prepare($query);
$stmt->execute([$prescription_id]);
$prescription = $stmt->fetch();

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
$items = $stmt->fetchAll();

if (!$prescription) {
    header("Location: index.php");
    exit;
}
?>

<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">
                Prescription #<?php echo $prescription['prescription_id']; ?>
            </h2>
            <p class="text-gray-600 mt-2">
                Created on <?php echo date('M d, Y', strtotime($prescription['prescription_date'])); ?>
            </p>
        </div>
        <div class="flex space-x-4">
            <a href="<?php echo $base_url; ?>prescriptions/edit_prescription.php?id=<?php echo $prescription_id; ?>" 
               class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <?php if ($prescription['status'] !== 'Filled'): ?>
                <a href="<?php echo $base_url; ?>prescriptions/fill_prescription.php?id=<?php echo $prescription_id; ?>" 
                   class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-prescription-bottle-alt mr-2"></i>Fill Prescription
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-8">
        <span class="px-3 py-1 text-sm font-semibold rounded-full 
            <?php echo $prescription['status'] === 'Filled' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
            <?php echo $prescription['status']; ?>
        </span>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- Patient Information -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-semibold mb-4">Patient Information</h3>
            <div class="space-y-3">
                <p><span class="font-medium">Name:</span> <?php echo htmlspecialchars($prescription['customer_name']); ?></p>
                <p><span class="font-medium">Contact:</span> <?php echo htmlspecialchars($prescription['customer_contact']); ?></p>
            </div>
        </div>

        <!-- Doctor Information -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-semibold mb-4">Doctor Information</h3>
            <div class="space-y-3">
                <p><span class="font-medium">Name:</span> <?php echo htmlspecialchars($prescription['doctor_name']); ?></p>
                <p><span class="font-medium">Specialization:</span> <?php echo htmlspecialchars($prescription['doctor_specialization']); ?></p>
                <p><span class="font-medium">Contact:</span> <?php echo htmlspecialchars($prescription['doctor_contact']); ?></p>
            </div>
        </div>
    </div>

    <!-- Prescription Items -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold">Prescribed Medications</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medication</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Form</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Instructions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($item['drug_name']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($item['dosage_form']); ?></td>
                        <td class="px-6 py-4"><?php echo $item['quantity']; ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($item['dosage_instructions']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Additional Information -->
    <div class="mt-8 text-sm text-gray-600">
        <p>Created by: <?php echo htmlspecialchars($prescription['created_by']); ?></p>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?> 