<?php
session_start();
require_once 'includes/db_connect.php';

if (isset($_SESSION['user_id'])) {
    // Log the logout action
    $stmt = $pdo->prepare("
        INSERT INTO AUDIT_LOG (user_id, timestamp, action, table_affected, record_id)
        VALUES (?, CURRENT_TIMESTAMP, 'LOGOUT', 'USER', ?)
    ");
    $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
}

// Clear all session variables
session_unset();
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;
?>
