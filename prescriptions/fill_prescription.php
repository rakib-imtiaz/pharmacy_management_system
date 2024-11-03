<?php
require_once '../includes/db_connect.php';
session_start();
include_once '../includes/header.php';

// Get prescription ID from URL
$prescription_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch prescription details with items
$query = "
    SELECT 
        p.*,
        c.name as customer_name,
        d.name as doctor_name,
        pi.prescription_item_id,
        pi.quantity as prescribed_quantity,
        pi.dosage_instructions,
        dr.drug_id,
        dr.name as drug_name,
        dr.dosage_form
    FROM PRESCRIPTION p
    JOIN CUSTOMER c ON p.customer_id = c.customer_id
    JOIN DOCTOR d ON p.doctor_id = d.doctor_id
    JOIN PRESCRIPTION_ITEM pi ON p.prescription_id = pi.prescription_id
    JOIN DRUG dr ON pi.drug_id = dr.drug_id
    WHERE p.prescription_id = ? AND p.status != 'Filled'
";

$stmt = $pdo->prepare($query);
$stmt->execute([$prescription_id]);
$items = $stmt->fetchAll();

if (empty($items)) {
    $_SESSION['error'] = "Invalid prescription or already filled";
    header("Location: index.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Update prescription status
        $update_prescription = $pdo->prepare("
            UPDATE PRESCRIPTION 
            SET status = 'Filled' 
            WHERE prescription_id = ?
        ");
        $update_prescription->execute([$prescription_id]);

        // Create counter sale
        $create_sale = $pdo->prepare("
            INSERT INTO COUNTER_SALE (customer_id, sale_date, total_amount, user_id)
            SELECT customer_id, CURRENT_DATE, 0, ?
            FROM PRESCRIPTION WHERE prescription_id = ?
        ");
        $create_sale->execute([$_SESSION['user_id'], $prescription_id]);
        $sale_id = $pdo->lastInsertId();

        $total_amount = 0;

        // Process each item
        foreach ($items as $item) {
            $drug_id = $item['drug_id'];
            $quantity = $item['prescribed_quantity'];

            // Get available stock
            $stock_query = $pdo->prepare("
                SELECT stock_item_id, quantity, unit_price
                FROM STOCK_ITEM
                WHERE drug_id = ? AND quantity >= ? AND expiry_date > CURRENT_DATE
                ORDER BY expiry_date ASC
                LIMIT 1
            ");
            $stock_query->execute([$drug_id, $quantity]);
            $stock = $stock_query->fetch();

            if (!$stock) {
                throw new Exception("Insufficient stock for " . $item['drug_name']);
            }

            // Update stock quantity
            $update_stock = $pdo->prepare("
                UPDATE STOCK_ITEM
                SET quantity = quantity - ?
                WHERE stock_item_id = ?
            ");
            $update_stock->execute([$quantity, $stock['stock_item_id']]);

            // Create counter sale item
            $create_sale_item = $pdo->prepare("
                INSERT INTO COUNTER_SALE_ITEM (sale_id, stock_item_id, quantity, unit_price)
                VALUES (?, ?, ?, ?)
            ");
            $create_sale_item->execute([
                $sale_id,
                $stock['stock_item_id'],
                $quantity,
                $stock['unit_price']
            ]);

            $total_amount += ($quantity * $stock['unit_price']);
        }

        // Update total amount
        $update_total = $pdo->prepare("
            UPDATE COUNTER_SALE
            SET total_amount = ?
            WHERE sale_id = ?
        ");
        $update_total->execute([$total_amount, $sale_id]);

        $pdo->commit();
        $_SESSION['success'] = "Prescription filled successfully";
        header("Location: view.php?id=" . $prescription_id);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = $e->getMessage();
    }
}
?>

<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Fill Prescription #<?php echo $prescription_id; ?></h2>
            <p class="text-gray-600 mt-2">
                Patient: <?php echo htmlspecialchars($items[0]['customer_name']); ?> |
                Doctor: <?php echo htmlspecialchars($items[0]['doctor_name']); ?>
            </p>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold">Prescribed Medications</h3>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medication</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Form</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instructions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($item['drug_name']); ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($item['dosage_form']); ?></td>
                        <td class="px-6 py-4"><?php echo $item['prescribed_quantity']; ?></td>
                        <td class="px-6 py-4"><?php echo htmlspecialchars($item['dosage_instructions']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="px-6 py-4 bg-gray-50 text-right">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Fill Prescription
            </button>
        </div>
    </form>
</div> 