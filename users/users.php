<?php
require_once '../includes/db_connect.php';
require_once '../includes/header.php';

$users = $pdo->query("SELECT * FROM USER ORDER BY user_id DESC")->fetchAll();
?>

<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Users Management</h2>
        <a href="user_add.php" class="bg-blue-500 text-white px-4 py-2 rounded">Add New User</a>
    </div>

    <div class="bg-white shadow-md rounded">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left">ID</th>
                    <th class="px-6 py-3 text-left">Username</th>
                    <th class="px-6 py-3 text-left">Role</th>
                    <th class="px-6 py-3 text-left">Last Login</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr class="border-b">
                    <td class="px-6 py-4"><?= htmlspecialchars($user['user_id']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($user['username']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($user['role']) ?></td>
                    <td class="px-6 py-4"><?= $user['last_login'] ? date('Y-m-d H:i', strtotime($user['last_login'])) : 'Never' ?></td>
                    <td class="px-6 py-4">
                        <a href="user_edit.php?id=<?= $user['user_id'] ?>" class="text-blue-500">Edit</a>
                        <a href="user_delete.php?id=<?= $user['user_id'] ?>" class="text-red-500 ml-2" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div> 