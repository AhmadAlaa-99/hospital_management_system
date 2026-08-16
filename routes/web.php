<?php

use App\Http\Controllers\AppointmentBookingController;
use App\Http\Controllers\AmbulanceRequestPublicController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\QueueDisplayController;
use App\Http\Controllers\QueueTrackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ], function () {

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
    Route::get('/blogs/{blog:slug}', [BlogController::class, 'show'])->name('blogs.show');
    Route::post('/blogs/{blog:slug}/like', [BlogController::class, 'like'])->name('blogs.like');
    Route::post('/blogs/{blog:slug}/comments', [BlogController::class, 'comment'])->name('blogs.comments');

    Route::post('/patient/auth/login', [\App\Http\Controllers\WebsitePatientAuthController::class, 'login'])
        ->name('website.patient.login');
    Route::post('/patient/auth/register', [\App\Http\Controllers\WebsitePatientAuthController::class, 'register'])
        ->name('website.patient.register');

    Route::get('/appointments/doctors', [AppointmentBookingController::class, 'doctors'])
        ->name('appointments.doctors');
    Route::get('/appointments/slots', [AppointmentBookingController::class, 'slots'])
        ->name('appointments.slots');
    Route::post('/appointments/book', [AppointmentBookingController::class, 'store'])
        ->middleware(['auth:patient', 'throttle:10,1'])
        ->name('appointments.book');

    Route::post('/ambulance/request', [AmbulanceRequestPublicController::class, 'store'])
        ->name('ambulance.request');

    Route::get('/queue/track', [QueueTrackController::class, 'show'])->name('queue.track');
    Route::post('/queue/track', [QueueTrackController::class, 'lookup'])
        ->middleware('throttle:30,1')
        ->name('queue.track.lookup');
    Route::get('/queue/display', [QueueDisplayController::class, 'index'])->name('queue.display.index');
    Route::get('/queue/display/section/{section}', [QueueDisplayController::class, 'section'])->name('queue.display.section');
    Route::get('/queue/display/doctor/{doctor}', [QueueDisplayController::class, 'doctor'])->name('queue.display.doctor');
    Route::get('/queue/data', [QueueDisplayController::class, 'data'])->name('queue.data');

});








