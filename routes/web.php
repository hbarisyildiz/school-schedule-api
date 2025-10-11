<?php

use Illuminate\Support\Facades\Route;

// Ana sayfa - Landing page
Route::get('/', function () {
    return response()->file(public_path('index.html'));
});

// Okul kayıt formu
Route::get('/school-registration', function () {
    return response()->file(public_path('school-registration.html'));
});

// Email doğrulama sayfası
Route::get('/verify-email', function () {
    return response()->file(public_path('verify-email.html'));
});

// Admin paneli - Modern version
Route::get('/admin-panel', function () {
    return response()->file(public_path('admin-panel-modern.html'));
});

// Eski admin panel (Backward compatibility)
Route::get('/admin-panel-old', function () {
    return response()->file(public_path('admin-panel.html'));
});

// Email doğrulama linki (GET parametreli)
Route::get('/verify-email/{token}', function ($token) {
    $html = file_get_contents(public_path('verify-email.html'));
    // Token'ı URL parametresine otomatik ekle
    $html = str_replace('window.location.search', "'?token=" . $token . "'", $html);
    return response($html)->header('Content-Type', 'text/html');
});
