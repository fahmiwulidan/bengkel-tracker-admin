<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\WorkshopCrudController;
use App\Http\Controllers\Admin\MechanicCrudController;
use App\Http\Controllers\Admin\UserCrudController;
use App\Http\Controllers\Admin\OrderCrudController;

Route::get('/', function () {
    return redirect('/admin');

});

Route::get('/login', function () {
})->name('login');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('workshops', WorkshopCrudController::class);
    Route::resource('mechanics', MechanicCrudController::class);
    Route::resource('users', UserCrudController::class);
    Route::get('orders', [OrderCrudController::class, 'index'])->name('orders.index');
    Route::get('orders/{id}', [OrderCrudController::class, 'show'])->name('orders.show');
});
