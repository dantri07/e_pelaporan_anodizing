<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SparePartController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\MachineReportController;
use App\Http\Controllers\MachineController;

Route::get('/', function () {
    return view('auth/login');
});

Auth::routes();


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);

    Route::resource('users', UserController::class);
    Route::resource('spare-parts', SparePartController::class);
    Route::resource('actions', ActionController::class);

    Route::resource('machine-reports', MachineReportController::class);

    Route::resource('machines', MachineController::class);

    Route::get('/api/low-stock-notifications', [SparePartController::class, 'getLowStockNotifications'])->name('api.low-stock-notifications');

    // Route to get the latest unread notification for the logged-in user
    Route::get('/notifications/latest', function () {
        $notification = auth()->user()->unreadNotifications()->latest()->first();
        return response()->json($notification);
    });

    // Route to mark a notification as read
    Route::post('/notifications/mark-as-read/{id}', function ($id) {
        $notification = auth()->user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['status' => 'Notification marked as read.']);
    });

    Route::delete('/machine-reports/media/{mediaId}', [MachineReportController::class, 'destroyMedia'])
        ->name('machine-reports.media.destroy');

    Route::delete('/machines/media/{mediaId}', [MachineController::class, 'destroyMedia'])
        ->name('machines.media.destroy');
});

Route::get('/logtest', function() {
    \Log::info('Log test berhasil!');
    return 'Log test done';
});