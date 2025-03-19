<?php
$title = 'Visitor Details';
$layout = 'master';
?>

<h1 class="text-2xl font-bold">Visitor Details</h1>
<p class="mt-4">IP Address: <?= $visitor->ip_address ?></p>
<p class="mt-4">Last Activity: <?= $visitor->last_activity ?></p>
<p class="mt-4">Status: <?= $visitor->is_active ? 'Active' : 'Inactive' ?></p>

<h2 class="text-xl font-bold mt-8">Forms Submitted</h2>
<table class="mt-4 w-full bg-white shadow-md rounded-lg">
    <thead>
        <tr>
            <th class="px-4 py-2">Form Data</th>
            <th class="px-4 py-2">Created At</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($forms as $form): ?>
            <tr>
                <td class="px-4 py-2"><?= $form->form_data ?></td>
                <td class="px-4 py-2"><?= $form->created_at ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>