<?php
require_once '../includes/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Don't allow deletion of the last administrator
    $admin_count = $pdo->query("SELECT COUNT(*) FROM USER WHERE role = 'Administrator'")->fetchColumn();
    $user = $pdo->query("SELECT role FROM USER WHERE user_id = $id")->fetch();
    
    if (!($admin_count <= 1 && $user['role'] == 'Administrator')) {
        $stmt = $pdo->prepare("DELETE FROM USER WHERE user_id = ?");
        $stmt->execute([$id]);
    }
}

header("Location: users.php");
exit();
?> 