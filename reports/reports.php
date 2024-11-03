<?php
require_once '../includes/db_connect.php';
session_start();

// Verify admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header("Location: ../index.php");
    exit;
}

include_once '../includes/header.php';

// Get date range from request or default to last 30 days
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Fetch sales summary
$sales_query = "
    SELECT 
        DATE(sale_date) as date,
        COUNT(*) as total_transactions,
        SUM(total_amount) as total_sales,
        AVG(total_amount) as average_sale
    FROM COUNTER_SALE 
    WHERE sale_date BETWEEN ? AND ?
    GROUP BY DATE(sale_date)
    ORDER BY date DESC
";

$sales_stmt = $pdo->prepare($sales_query);
$sales_stmt->execute([$start_date, $end_date]);
$sales_data = $sales_stmt->fetchAll();

// Fetch top selling products
$products_query = "
    SELECT 
        d.name as drug_name,
        SUM(csi.quantity) as total_quantity,
        SUM(csi.quantity * csi.unit_price) as total_revenue
    FROM COUNTER_SALE_ITEM csi
    JOIN STOCK_ITEM si ON csi.stock_item_id = si.stock_item_id
    JOIN DRUG d ON si.drug_id = d.drug_id
    JOIN COUNTER_SALE cs ON csi.sale_id = cs.sale_id
    WHERE cs.sale_date BETWEEN ? AND ?
    GROUP BY d.drug_id
    ORDER BY total_quantity DESC
    LIMIT 10
";

$products_stmt = $pdo->prepare($products_query);
$products_stmt->execute([$start_date, $end_date]);
$top_products = $products_stmt->fetchAll();

// Fetch cashier performance
$cashier_query = "
    SELECT 
        u.username,
        COUNT(cs.sale_id) as total_sales,
        SUM(cs.total_amount) as total_revenue,
        AVG(cs.total_amount) as average_sale
    FROM USER u
    LEFT JOIN COUNTER_SALE cs ON u.user_id = cs.user_id
    WHERE cs.sale_date BETWEEN ? AND ?
    GROUP BY u.user_id
    ORDER BY total_revenue DESC
";

$cashier_stmt = $pdo->prepare($cashier_query);
$cashier_stmt->execute([$start_date, $end_date]);
$cashier_data = $cashier_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Reports & Analytics</h1>
            
            <!-- Date Range Filter -->
            <form class="flex items-center space-x-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="start_date">Start Date</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="end_date">End Date</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Apply</button>
            </form>
        </div>
    </div>
</body>
</html> 