<?php
require_once '../includes/db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM CUSTOMER WHERE customer_id = ?");
    $stmt->execute([$id]);
}

header("Location: customers.php");
exit();
?> 