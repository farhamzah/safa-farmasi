<?php

use App\Http\Controllers\Admin\ApplicationExportController;
use App\Http\Controllers\Admin\CategoryExportController;
use App\Http\Controllers\Admin\ClickExportController;
use App\Http\Controllers\ApplicationRedirectController;
use App\Http\Controllers\LandingPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPageController::class)->name('landing');
Route::get('/go/{portalApplication:slug}', ApplicationRedirectController::class)->name('applications.go');

Route::prefix('admin/exports')->name('admin.exports.')->group(function (): void {
    Route::get('/applications', ApplicationExportController::class)->name('applications');
    Route::get('/categories', CategoryExportController::class)->name('categories');
    Route::get('/clicks', ClickExportController::class)->name('clicks');
});
