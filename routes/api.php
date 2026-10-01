<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WorkshopController;
use App\Http\Controllers\Api\MechanicController;
use App\Http\Controllers\Api\KendaraanController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ChatRoomController;
use App\Http\Controllers\Api\ChatMessageController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\Admin\UserCrudController;

use App\Http\Middleware;
/*
|--------------------------------------------------------------------------
| Public Routes (Bisa diakses tanpa login / Tanpa Token)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Webhook harus ditaruh di LUAR middleware auth:sanctum karena dipanggil oleh server Xendit
Route::post('/payments/webhook', [PaymentController::class, 'webhook']);


/*
|--------------------------------------------------------------------------
| Protected Routes (Wajib membawa Bearer Token Sanctum di Postman)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // --- Authentication ---
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- Workshops (Bengkel) ---
    Route::get('/workshops', [WorkshopController::class, 'index']);
    Route::get('/workshops/open', [WorkshopController::class, 'open']);
    Route::get('/workshops/top-rated', [WorkshopController::class, 'topRated']);
    Route::get('/workshops/nearest', [WorkshopController::class, 'nearest']);
    Route::get('/workshops/filter', [WorkshopController::class, 'filter']);
    Route::get('/workshops/{id}', [WorkshopController::class, 'show']);
    Route::get('/workshops/{workshopId}/mechanics', [MechanicController::class, 'byWorkshop']);

    // --- Mechanics (Mekanik) ---
    Route::get('/mechanics', [MechanicController::class, 'index']);
    Route::get('/mechanics/nearest', [MechanicController::class, 'nearest']);
    Route::get('/mechanics/{id}/location', [MechanicController::class, 'showLocation']);
    Route::patch('/mechanics/{id}/location', [MechanicController::class, 'updateLocation']);
   Route::patch('/mechanics/{id}/status',[MechanicController::class, 'updateStatus']);
    Route::put('/profile/update',[MechanicController::class, 'updateProfile']);
        Route::get('/mechanic/statistic', [OrderController::class, 'mechanicStatistic']);
    // Fitur Tambah Mekanik (Admin Office / Backoffice)
    Route::post('/mechanics/admin-store', [MechanicController::class, 'store']);
// Users Route Api
Route::get('/users', [UserCrudController::class, 'index']);
Route::post('/users', [UserCrudController::class, 'store']);
Route::get('/users/{id}/edit', [UserCrudController::class, 'edit']);
Route::put('/users/{id}', [UserCrudController::class, 'update']);
Route::delete('/users/{id}', [UserCrudController::class, 'destroy']);

    // --- Kendaraan ---
    Route::get('/kendaraan', [KendaraanController::class, 'index']);
    Route::post('/kendaraan', [KendaraanController::class, 'store']);
    Route::get('/kendaraan/{id}', [KendaraanController::class, 'show']);

    // --- Orders & Tracking (Duplikasi Sudah Dihapus) ---
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}/tracking', [OrderController::class, 'tracking']);

    // --- Chat Room API ---
    Route::post('/chat-rooms', [ChatRoomController::class, 'store']);
    Route::get('/chat-rooms/{id}', [ChatRoomController::class, 'show']);
Route::get('/chat-rooms', [ChatRoomController::class, 'index']);
    // --- Chat Message API ---
    Route::get('/chat-rooms/{chat_room_id}/messages', [ChatMessageController::class, 'index']);
    Route::post('/chat-messages', [ChatMessageController::class, 'store']);

    // --- Payment API (Hanya Request Invoice yang Butuh Auth) ---
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::post('/payments/create-invoice', [PaymentController::class, 'createInvoice']);
    // --- Order Item API ---
    Route::get('/orders/{order_id}/items', [OrderItemController::class, 'index']);
    Route::post('/order-items', [OrderItemController::class, 'store']);
Route::get('/orders', [OrderController::class, 'index']); // List semua order user
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    // --- Log API ---
    Route::get('/orders/{order_id}/logs', [LogController::class, 'index']);

    // Route::get('/mechanic/orders', [OrderCrudController::class, 'mechanicOrders'])
//--- order ---
    Route::post('/orders/{id}/accept', [OrderController::class, 'accept']);
    Route::post('/orders/{id}/arrive', [OrderController::class, 'arrive']);
    Route::post('/orders/{id}/complete', [OrderController::class, 'complete']);

    //--- terbaru ---/
    Route::patch('/mechanics/{id}/status',[MechanicController::class, 'updateStatus']);
    Route::get('/mechanic/orders', [OrderController::class, 'mechanicOrders']);

});
