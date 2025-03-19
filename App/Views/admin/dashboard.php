<?php
$title = 'Admin Dashboard';
$layout = 'master';
?>

<h1 class="text-2xl font-bold">Admin Dashboard</h1>
<table class="mt-4 w-full bg-white shadow-md rounded-lg">
    <thead>
        <tr>
            <th class="px-4 py-2">Visitor IP</th>
            <th class="px-4 py-2">Last Activity</th>
            <th class="px-4 py-2">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($visitors as $visitor): ?>
            <tr>
                <td class="px-4 py-2"><?= $visitor->ip_address ?></td>
                <td class="px-4 py-2"><?= $visitor->last_activity ?></td>
                <td class="px-4 py-2"><?= $visitor->is_active ? 'Active' : 'Inactive' ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>