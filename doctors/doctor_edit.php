<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize_input($_POST['name']);
    $specialization = sanitize_input($_POST['specialization']);
    $contact_info = sanitize_input($_POST['contact_info']);
    
    $stmt = $pdo->prepare("UPDATE DOCTOR SET name = ?, specialization = ?, contact_info = ? WHERE doctor_id = ?");
    if ($stmt->execute([$name, $specialization, $contact_info, $id])) {
        // Log the action
        log_audit($pdo, 1, 'UPDATE', 'DOCTOR', $id); // Assuming user_id 1 for now
        
        header("Location: doctors.php");
        exit();
    }
}

$doctor = $pdo->query("SELECT * FROM DOCTOR WHERE doctor_id = $id")->fetch();
?>

<div class="container mx-auto">
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-6">Edit Doctor</h2>
        
        <form method="POST">
            <div class="mb-4">
                <label class="block mb-2">Name</label>
                <input type="text" name="name" required 
                       value="<?= htmlspecialchars($doctor['name']) ?>"
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Specialization</label>
                <input type="text" name="specialization" required 
                       value="<?= htmlspecialchars($doctor['specialization']) ?>"
                       class="w-full px-3 py-2 border rounded">
            </div>
            
            <div class="mb-4">
                <label class="block mb-2">Contact Info</label>
                <textarea name="contact_info" required 
                          class="w-full px-3 py-2 border rounded"
                          rows="3"><?= htmlspecialchars($doctor['contact_info']) ?></textarea>
            </div>
            
            <div class="flex justify-between">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Save Doctor
                </button>
                <a href="doctors.php" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 