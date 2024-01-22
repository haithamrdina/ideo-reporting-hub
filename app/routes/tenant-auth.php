<?php

use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/home', 'App\Http\Controllers\Tenant\HomeController@index')->name('home');

// Login
Route::get('login', 'App\Http\Controllers\Tenant\Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'App\Http\Controllers\Tenant\Auth\LoginController@login');
Route::post('logout', 'App\Http\Controllers\Tenant\Auth\LoginController@logout')->name('logout');

// Register
Route::get('register', 'App\Http\Controllers\Tenant\Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'App\Http\Controllers\Tenant\Auth\RegisterController@register');

// Reset Password
Route::get('password/reset', 'App\Http\Controllers\Tenant\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'App\Http\Controllers\Tenant\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'App\Http\Controllers\Tenant\Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'App\Http\Controllers\Tenant\Auth\ResetPasswordController@reset')->name('password.update');

// Confirm Password
Route::get('password/confirm', 'App\Http\Controllers\Tenant\Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
Route::post('password/confirm', 'App\Http\Controllers\Tenant\Auth\ConfirmPasswordController@confirm');

// Verify Email
// Route::get('email/verify', 'App\Http\Controllers\Tenant\Auth\VerificationController@show')->name('verification.notice');
// Route::get('email/verify/{id}/{hash}', 'App\Http\Controllers\Tenant\Auth\VerificationController@verify')->name('verification.verify');
// Route::post('email/resend', 'App\Http\Controllers\Tenant\Auth\VerificationController@resend')->name('verification.resend');
