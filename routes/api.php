<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ContentResourceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnquiryController as AdminEnquiryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\ContextController;
use App\Http\Controllers\Public\EnquiryController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\SitePageController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::get('/context', ContextController::class);
    Route::get('/public/home', HomeController::class);
    Route::get('/public/information/{slug}', [SitePageController::class, 'information']);
    Route::get('/public/programs', [SitePageController::class, 'programs']);
    Route::get('/public/programs/{slug}', [SitePageController::class, 'program']);
    Route::get('/public/people/{group}', [SitePageController::class, 'people']);
    Route::get('/public/facilities', [SitePageController::class, 'facilities']);
    Route::get('/public/notices', [SitePageController::class, 'notices']);
    Route::get('/public/notices/{slug}', [SitePageController::class, 'notice']);
    Route::get('/public/news', [SitePageController::class, 'news']);
    Route::get('/public/news/{slug}', [SitePageController::class, 'newsPost']);
    Route::get('/public/events', [SitePageController::class, 'events']);
    Route::get('/public/events/{slug}', [SitePageController::class, 'event']);
    Route::get('/public/gallery', [SitePageController::class, 'gallery']);
    Route::get('/public/downloads', [SitePageController::class, 'downloads']);
    Route::get('/public/contact', [SitePageController::class, 'contact']);
    Route::get('/public/careers', [SitePageController::class, 'careers']);
    Route::post('/public/admission-enquiries', [EnquiryController::class, 'admission']);
    Route::post('/public/enquiries', [EnquiryController::class, 'general']);

    Route::post('/auth/login', [AuthController::class, 'store']);
    Route::post('/auth/logout', [AuthController::class, 'destroy'])->middleware('auth');

    Route::prefix('admin')->middleware('admin')->group(function (): void {
        Route::get('/dashboard', DashboardController::class);
        Route::get('/content/{resource}', [ContentResourceController::class, 'index']);
        Route::post('/content/{resource}', [ContentResourceController::class, 'store']);
        Route::patch('/content/{resource}/{id}', [ContentResourceController::class, 'update'])->whereNumber('id');
        Route::delete('/content/{resource}/{id}', [ContentResourceController::class, 'destroy'])->whereNumber('id');
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::post('/users', [AdminUserController::class, 'store']);
        Route::patch('/users/{user}', [AdminUserController::class, 'update']);
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);
        Route::get('/enquiries', [AdminEnquiryController::class, 'index']);
        Route::patch('/enquiries/admission/{enquiry}', [AdminEnquiryController::class, 'updateAdmission']);
        Route::patch('/enquiries/general/{enquiry}', [AdminEnquiryController::class, 'updateGeneral']);
    });
});
