<?php

use App\Http\Controllers\Tenant\Auth\ConfirmPasswordController;
use App\Http\Controllers\Tenant\Auth\ForgotPasswordController;
use App\Http\Controllers\Tenant\Auth\LoginController;
use App\Http\Controllers\Tenant\Auth\RegisterController;
use App\Http\Controllers\Tenant\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;



// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('logout',  [LoginController::class, 'logout'])->name('logout');

// Register
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register',  [RegisterController::class, 'register']);

// Reset Password
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email',  [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}',  [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Confirm Password
Route::get('password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
Route::post('password/confirm',  [ConfirmPasswordController::class, 'confirm']);
// Verify Email
// Route::get('email/verify', 'App\Http\Controllers\Tenant\Auth\VerificationController@show')->name('verification.notice');
// Route::get('email/verify/{id}/{hash}', 'App\Http\Controllers\Tenant\Auth\VerificationController@verify')->name('verification.verify');
// Route::post('email/resend', 'App\Http\Controllers\Tenant\Auth\VerificationController@resend')->name('verification.resend');
