<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TwoFactorController;
use Illuminate\Auth\Events\Login;
use App\Http\Controllers\Auth\LoginRegisterController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and assigned to the "web"
| middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Show login form
Route::get('/login', [LoginRegisterController::class, 'showLoginForm'])->name('login.form');

// Handle login form submission
Route::post('/login', [LoginRegisterController::class, 'login'])->name('login');

// Show registration form
Route::get('/register', [LoginRegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginRegisterController::class, 'register']);


Route::get('/landing-page', [LoginRegisterController::class, 'landingPage'])->name('landing.page');

Route::get('/2fa', [TwoFactorController::class, 'show'])->name('2fa');

Route::post('/2fa', [TwoFactorController::class, 'verify'])->name('2fa.verify');


Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::post('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{product}/edit', [ProductController::class, 'update'])->name('products.edit');
Route::post('/products/{id}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
Route::post('/logout', [LoginRegisterController::class, 'logout'])->name('logout');
