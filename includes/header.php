<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Pharmacy System</h1>
                <div class="space-x-4">
                    <a href="<?php echo $base_url; ?>" class="hover:text-gray-200">Home</a>
                    <a href="<?php echo $base_url; ?>drugs/drugs.php" class="hover:text-gray-200">Drugs</a>
                    <a href="<?php echo $base_url; ?>customers/customers.php" class="hover:text-gray-200">Customers</a>
                    <a href="<?php echo $base_url; ?>prescriptions/prescriptions.php" class="hover:text-gray-200">Prescriptions</a>
                    <a href="<?php echo $base_url; ?>sales/sales.php" class="hover:text-gray-200">Sales</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="container mx-auto p-4">