<?php
require_once '../includes/db_connect.php';


// Handle doctor deletion
if (isset($_POST['delete_doctor'])) {
    $doctor_id = $_POST['doctor_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM DOCTOR WHERE doctor_id = ?");
        $stmt->execute([$doctor_id]);
        $_SESSION['success'] = "Doctor deleted successfully";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting doctor: " . $e->getMessage();
    }
    header("Location: " . $base_url . "doctors/doctors.php");
    exit();
}

// Fetch all doctors
$stmt = $pdo->query("SELECT * FROM DOCTOR ORDER BY name");
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include_once '../includes/header.php'; ?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Manage Doctors</h1>
        <a href="<?php echo $base_url; ?>doctors/add_doctor.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-plus mr-2"></i>Add New Doctor
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialization</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($doctors as $doctor): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($doctor['name']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($doctor['specialization']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($doctor['contact_info']); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex space-x-2">
                                <a href="<?php echo $base_url; ?>doctors/edit_doctor.php?id=<?php echo $doctor['doctor_id']; ?>" 
                                   class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo $base_url; ?>doctors/doctors.php" method="POST" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this doctor?');">
                                    <input type="hidden" name="doctor_id" value="<?php echo $doctor['doctor_id']; ?>">
                                    <button type="submit" name="delete_doctor" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 