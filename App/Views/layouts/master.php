<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? config('app.name') ?></title>
    <link href="/assets/css/tailwind.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <header class="bg-blue-600 text-white p-4">
        <h1 class="text-2xl font-bold"><?= config('app.name') ?></h1>
    </header>

    <div class="container mx-auto p-4">
        <?php renderSection('content'); ?>
    </div>

    <footer class="bg-gray-800 text-white p-4 mt-8">
        <p class="text-center">© <?= date('Y') ?> <?= config('app.name') ?>. All rights reserved.</p>
    </footer>
</body>

</html>