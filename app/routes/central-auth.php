<?php

use Illuminate\Support\Facades\Route;

// Dashboard
Route::get('/home', 'App\Http\Controllers\Central\HomeController@index')->name('home');

// Login
Route::get('login', 'App\Http\Controllers\Central\Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'App\Http\Controllers\Central\Auth\LoginController@login');
Route::post('logout', 'App\Http\Controllers\Central\Auth\LoginController@logout')->name('logout');

// Register
Route::get('register', 'App\Http\Controllers\Central\Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'App\Http\Controllers\Central\Auth\RegisterController@register');

// Reset Password
Route::get('password/reset', 'App\Http\Controllers\Central\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'App\Http\Controllers\Central\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'App\Http\Controllers\Central\Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'App\Http\Controllers\Central\Auth\ResetPasswordController@reset')->name('password.update');

// Confirm Password
Route::get('password/confirm', 'App\Http\Controllers\Central\Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
Route::post('password/confirm', 'App\Http\Controllers\Central\Auth\ConfirmPasswordController@confirm');

// Verify Email
// Route::get('email/verify', 'App\Http\Controllers\Central\Auth\VerificationController@show')->name('verification.notice');
// Route::get('email/verify/{id}/{hash}', 'App\Http\Controllers\Central\Auth\VerificationController@verify')->name('verification.verify');
// Route::post('email/resend', 'App\Http\Controllers\Central\Auth\VerificationController@resend')->name('verification.resend');
