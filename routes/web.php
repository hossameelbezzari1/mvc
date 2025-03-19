<?php

use App\Routing\Route;
use App\Controllers\HomeController;
use App\Controllers\FormController;
use App\Controllers\AdminController;

// مسار خارج المجموعة
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/hello', function () {
    return 'hello page';
})->name('hello');

// مجموعة مسارات للوحة التحكم مع ميدل وير
Route::group(['middleware' => ['auth', 'log']], function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/visitor/{id}', [AdminController::class, 'visitorDetails'])->name('admin.visitor_details');
});

// مسار خارجي آخر
Route::post('/submit-form', [FormController::class, 'submit'])->name('submit_form');