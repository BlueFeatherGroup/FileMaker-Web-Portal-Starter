<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return Inertia::render('Welcome', [
//        'canLogin' => Route::has('login'),
//        'canRegister' => Route::has('register'),
//        'laravelVersion' => Application::VERSION,
//        'phpVersion' => PHP_VERSION,
//    ]);
//});

Route::get('/', function () {
    return redirect()->route('dashboard');
});


Route::middleware(['auth:sanctum', 'verified', 'client.linked'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard');
    Route::get('/dashboard/paid-invoices', [DashboardController::class, 'getPaidInvoices'])->name('dashboard-paid-invoices');
    Route::get('/invoice/{id}', [InvoiceController::class, 'get'])->name('invoice.detail');
    Route::get('/invoice/{id}/pay', [InvoiceController::class, 'pay'])->name('invoice.pay');
    Route::post('/invoice/{id}/pay', [InvoiceController::class, 'submitPayment'])->name('invoice.pay.submit');
    Route::get('/invoice/{id}/pay/success', [InvoiceController::class, 'success'])->name('invoice.pay.success');
});

Route::middleware(['web', 'verified', 'auth:sanctum'])->group(function(){
    Route::get('/user/account-link', [AccountController::class, 'showLinkAccount'])->name('user.link-account');
    Route::post('/user/account-link', [AccountController::class, 'storeLinkAccount'])->name('user.link-account.store');
});

