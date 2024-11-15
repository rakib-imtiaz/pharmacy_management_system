<?php
require_once '../includes/db_connect.php';
session_start();

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // Check if the customer has any associated prescriptions or sales
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM PRESCRIPTION WHERE customer_id = :customer_id");
        $check_stmt->execute([':customer_id' => $id]);
        $prescription_count = $check_stmt->fetchColumn();

        $check_sales_stmt = $pdo->prepare("SELECT COUNT(*) FROM COUNTER_SALE WHERE customer_id = :customer_id");
        $check_sales_stmt->execute([':customer_id' => $id]);
        $sales_count = $check_sales_stmt->fetchColumn();

        if ($prescription_count == 0 && $sales_count == 0) {
            // If no prescriptions or sales, proceed to delete the customer
            $delete_stmt = $pdo->prepare("DELETE FROM CUSTOMER WHERE customer_id = :customer_id");
            if ($delete_stmt->execute([':customer_id' => $id])) {
                $_SESSION['success_message'] = "Customer deleted successfully.";
            } else {
                $_SESSION['error_message'] = "Failed to delete customer.";
            }
        } else {
            $_SESSION['error_message'] = "Cannot delete customer with associated prescriptions or sales.";
        }
    } catch (PDOException $e) {
        error_log("Error deleting customer: " . $e->getMessage());
        $_SESSION['error_message'] = "An error occurred. Please try again later.";
    }
}

header("Location: customers.php");
exit();
