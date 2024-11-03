<?php
require_once '../includes/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Check if doctor has any associated prescriptions
    $prescription_count = $pdo->query("SELECT COUNT(*) FROM PRESCRIPTION WHERE doctor_id = $id")->fetchColumn();
    
    if ($prescription_count == 0) {
        $stmt = $pdo->prepare("DELETE FROM DOCTOR WHERE doctor_id = ?");
        if ($stmt->execute([$id])) {
            // Log the action
            log_audit($pdo, 1, 'DELETE', 'DOCTOR', $id); // Assuming user_id 1 for now
        }
    } else {
        // Set a session message about unable to delete
        session_start();
        $_SESSION['error_message'] = "Cannot delete doctor: There are associated prescriptions.";
    }
}

header("Location: doctors.php");
exit();
?> 