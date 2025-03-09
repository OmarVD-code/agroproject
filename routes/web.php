<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return view('auth/login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/products', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/sales', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/customers', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/suppliers', [App\Http\Controllers\HomeController::class, 'index']);
Route::get('/reports', [App\Http\Controllers\HomeController::class, 'index']);

// GETTERS
// SUPPLIERS
Route::get('/suppliers-list', [SuppliersController::class, 'index'])->name('suppliers.index');

// PRODUCTS
Route::post('/products-list', [ProductsController::class, 'index'])->name('products.index');
Route::get('/products-get', [ProductsController::class, 'getProducts'])->name('products.getProducts');
Route::post('/products-add', [ProductsController::class, 'store'])->name('products.store');
Route::post('/products-edit', [ProductsController::class, 'update'])->name('products.update');

// CUSTOMERS
Route::post('/customers-list', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers-get', [CustomerController::class, 'getCustomers'])->name('customers.getCustomers');
Route::post('/customers-add', [CustomerController::class, 'store'])->name('customers.store');
Route::post('/customers-edit', [CustomerController::class, 'update'])->name('customers.update');

// SALES
Route::post('/sales-list', [SalesController::class, 'index'])->name('sales.index');
Route::post('/sales-add', [SalesController::class, 'store'])->name('sales.store');
Route::post('/sales-update-status', [SalesController::class, 'update'])->name('sales.update');
Route::post('/sales-details', [SalesController::class, 'details'])->name('sales.details');

// ADRESSES
Route::get('/address-list', [AddressController::class, 'index'])->name('address.index');
Route::post('/address-add', [AddressController::class, 'store'])->name('address.store');