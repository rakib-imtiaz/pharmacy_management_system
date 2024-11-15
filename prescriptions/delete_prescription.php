<?php
require_once '../includes/db_connect.php';
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['error'] = "Unauthorized access. Please log in.";
    header("Location: ../login.php");
    exit;
}

// Fetch user information
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$is_admin = ($user_role === 'Administrator');
$is_doctor = ($user_role === 'Doctor');

// Get the prescription ID from the query string
$prescription_id = $_GET['id'] ?? null;

if (!$prescription_id) {
    $_SESSION['error'] = "No prescription ID provided.";
    header("Location: ../prescriptions/prescriptions.php");
    exit;
}

// Fetch the prescription to confirm it exists and determine the doctor ID
$prescription_stmt = $pdo->prepare("SELECT doctor_id FROM PRESCRIPTION WHERE prescription_id = ?");
$prescription_stmt->execute([$prescription_id]);
$prescription = $prescription_stmt->fetch();

if (!$prescription) {
    $_SESSION['error'] = "Prescription not found.";
    header("Location: ../prescriptions/prescriptions.php");
    exit;
}

// Check if the user is allowed to delete this prescription
if (!$is_admin && !($is_doctor && $prescription['doctor_id'] == $user_id)) {
    $_SESSION['error'] = "You are not authorized to delete this prescription.";
    header("Location: ../prescriptions/prescriptions.php");
    exit;
}

// Delete the prescription and its associated items
try {
    $pdo->beginTransaction();

    // Delete prescription items first (to maintain referential integrity)
    $delete_items_stmt = $pdo->prepare("DELETE FROM PRESCRIPTION_ITEM WHERE prescription_id = ?");
    $delete_items_stmt->execute([$prescription_id]);

    // Delete the prescription itself
    $delete_prescription_stmt = $pdo->prepare("DELETE FROM PRESCRIPTION WHERE prescription_id = ?");
    $delete_prescription_stmt->execute([$prescription_id]);

    $pdo->commit();
    $_SESSION['success'] = "Prescription deleted successfully.";
    header("Location: ../prescriptions/prescriptions.php");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Error deleting prescription: " . $e->getMessage();
    header("Location: ../prescriptions/prescriptions.php");
    exit;
}
