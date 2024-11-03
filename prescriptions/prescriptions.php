<?php
require_once '../includes/db_connect.php';
session_start();
include_once '../includes/header.php';

// Fetch prescriptions with related information
$query = "
    SELECT 
        p.prescription_id,
        p.prescription_date,
        p.status,
        c.name as customer_name,
        d.name as doctor_name,
        u.username as created_by
    FROM PRESCRIPTION p
    JOIN CUSTOMER c ON p.customer_id = c.customer_id
    JOIN DOCTOR d ON p.doctor_id = d.doctor_id
    JOIN USER u ON p.user_id = u.user_id
    ORDER BY p.prescription_date DESC
";
$prescriptions = $pdo->query($query)->fetchAll();
?>

<div class="relative min-h-screen">
    <div class="container mx-auto px-6 py-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">
                Prescriptions Management
            </h2>
            <a href="<?php echo $base_url; ?>prescriptions/create_new_prescription.php" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
                <i class="fas fa-plus mr-2"></i>New Prescription
            </a>
        </div>

        <!-- Prescriptions Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($prescriptions as $prescription): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo $prescription['prescription_id']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo date('M d, Y', strtotime($prescription['prescription_date'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($prescription['customer_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($prescription['doctor_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $prescription['status'] === 'Filled' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                    <?php echo $prescription['status']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($prescription['created_by']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="<?php echo $base_url; ?>prescriptions/view_prescription.php?id=<?php echo $prescription['prescription_id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo $base_url; ?>prescriptions/edit_prescription.php?id=<?php echo $prescription['prescription_id']; ?>" 
                                   class="text-green-600 hover:text-green-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($prescription['status'] !== 'Filled'): ?>
                                    <a href="<?php echo $base_url; ?>prescriptions/fill_prescription.php?id=<?php echo $prescription['prescription_id']; ?>" 
                                       class="text-purple-600 hover:text-purple-900">
                                        <i class="fas fa-prescription-bottle-alt"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?> 