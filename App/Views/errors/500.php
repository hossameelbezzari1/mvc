<?php extend('layouts/error'); ?>

<?php section('content'); ?>
<div class="container mx-auto p-4 text-center">
    <h1 class="text-4xl font-bold text-red-600">500 - Internal Server Error</h1>
    <p class="mt-4 text-gray-700">Something went wrong on our end. Please try again later.</p>
    <a href="/" class="mt-6 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg">Go Home</a>
</div>
<?php endSection(); ?>