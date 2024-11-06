<?php
// Start with database connection and session
require_once '../includes/db_connect.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $contact_info = $_POST['contact_info'];

    try {
        $stmt = $pdo->prepare("INSERT INTO DOCTOR (name, specialization, contact_info) VALUES (?, ?, ?)");
        $stmt->execute([$name, $specialization, $contact_info]);
        
        $_SESSION['success'] = "Doctor added successfully";
        // Make sure there's no whitespace or output before this header
        header("Location: " . $base_url . "doctors/doctors.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding doctor: " . $e->getMessage();
    }
}

// Include header after processing form submission
require_once '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Add New Doctor</h1>
            <a href="<?php echo $base_url; ?>doctors/doctors.php" class="text-blue-500 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to Doctors
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="" method="POST">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" name="name" id="name" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="specialization" class="block text-gray-700 text-sm font-bold mb-2">Specialization</label>
                    <input type="text" name="specialization" id="specialization"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-6">
                    <label for="contact_info" class="block text-gray-700 text-sm font-bold mb-2">Contact Info</label>
                    <input type="text" name="contact_info" id="contact_info"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Add Doctor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 