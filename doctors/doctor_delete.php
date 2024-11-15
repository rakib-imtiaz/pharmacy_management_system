<?php
require_once '../includes/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        // Prepare a statement to count associated prescriptions
        $prescription_stmt = $pdo->prepare("SELECT COUNT(*) FROM PRESCRIPTION WHERE doctor_id = :doctor_id");
        $prescription_stmt->bindParam(':doctor_id', $id, PDO::PARAM_INT);
        $prescription_stmt->execute();
        $prescription_count = $prescription_stmt->fetchColumn();

        if ($prescription_count == 0) {
            // No associated prescriptions, proceed to delete
            $delete_stmt = $pdo->prepare("DELETE FROM DOCTOR WHERE doctor_id = :doctor_id");
            if ($delete_stmt->execute([':doctor_id' => $id])) {
                // Log the action
                log_audit($pdo, 1, 'DELETE', 'DOCTOR', $id); // Assuming user_id 1 for now
            }
        } else {
            // Set a session message about unable to delete
            session_start();
            $_SESSION['error_message'] = "Cannot delete doctor: There are associated prescriptions.";
        }
    } catch (PDOException $e) {
        // Handle exception if any
        error_log("Error deleting doctor: " . $e->getMessage());
        session_start();
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
    }
}

header("Location: doctors.php");
exit();
