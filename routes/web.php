<?php

use App\Http\Controllers\Public\EnquiryController;
use Illuminate\Support\Facades\Route;

Route::post('/admission-enquiries', [EnquiryController::class, 'admission']);
Route::post('/enquiries', [EnquiryController::class, 'general']);

/* React owns browser navigation. Laravel serves one shell for every SPA route. */
Route::view('/{any?}', 'app')->where('any', '.*')->name('frontend');
