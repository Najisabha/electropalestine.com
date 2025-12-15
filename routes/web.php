<?php

use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminCatalogController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StoreController::class, 'home'])->name('home');
Route::get('/products/{product:slug}', [StoreController::class, 'product'])->name('products.show');

Route::redirect('/dashboard', '/admin/dashboard');

Route::middleware('auth')->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/catalog', [AdminCatalogController::class, 'index'])->name('catalog');
        Route::get('/catalog/categories', [AdminCatalogController::class, 'categoriesPage'])->name('catalog.categories');
        Route::get('/catalog/types', [AdminCatalogController::class, 'typesPage'])->name('catalog.types');
        Route::get('/catalog/companies', [AdminCatalogController::class, 'companiesPage'])->name('catalog.companies');
    Route::post('/catalog/category', [AdminCatalogController::class, 'storeCategory'])->name('catalog.category');
    Route::post('/catalog/type', [AdminCatalogController::class, 'storeType'])->name('catalog.type');
    Route::post('/catalog/company', [AdminCatalogController::class, 'storeCompany'])->name('catalog.company');
    Route::post('/catalog/product', [AdminCatalogController::class, 'storeProduct'])->name('catalog.product');
    Route::put('/catalog/category/{category}', [AdminCatalogController::class, 'updateCategory'])->name('catalog.category.update');
    Route::put('/catalog/type/{type}', [AdminCatalogController::class, 'updateType'])->name('catalog.type.update');
    Route::put('/catalog/company/{company}', [AdminCatalogController::class, 'updateCompany'])->name('catalog.company.update');
    Route::put('/catalog/product/{product}', [AdminCatalogController::class, 'updateProduct'])->name('catalog.product.update');
    Route::delete('/catalog/category/{category}', [AdminCatalogController::class, 'destroyCategory'])->name('catalog.category.delete');
    Route::delete('/catalog/type/{type}', [AdminCatalogController::class, 'destroyType'])->name('catalog.type.delete');
    Route::delete('/catalog/company/{company}', [AdminCatalogController::class, 'destroyCompany'])->name('catalog.company.delete');
    Route::delete('/catalog/product/{product}', [AdminCatalogController::class, 'destroyProduct'])->name('catalog.product.delete');
    Route::view('/add-product', 'pages.add-product')->name('add-product');
    Route::view('/select-product', 'pages.select-product')->name('select-product');
    Route::view('/add-campaign', 'pages.add-campaign')->name('add-campaign');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');
    Route::view('/coupons', 'pages.coupons')->name('coupons');
    Route::view('/store-settings', 'pages.store-settings')->name('store-settings');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgot'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
