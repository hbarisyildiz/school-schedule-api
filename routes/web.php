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

// Admin paneli
Route::get('/admin-panel', function () {
    return response()->file(public_path('admin-panel.html'));
});

// Sınıf düzenleme sayfası
Route::get('/edit-class.html', function () {
    return response()->file(public_path('edit-class.html'));
});

// Sınıf ekleme sayfası
Route::get('/add-class.html', function () {
    return response()->file(public_path('add-class.html'));
});

// Derslik ekleme sayfası
Route::get('/add-area.html', function () {
    return response()->file(public_path('add-area.html'));
});

// Derslik düzenleme sayfası
Route::get('/edit-area.html', function () {
    return response()->file(public_path('edit-area.html'));
});

// Sınıf saatleri sayfası
Route::get('/class-schedule.html', function () {
    return response()->file(public_path('class-schedule.html'));
});

// Öğretmen ekleme sayfası
Route::get('/add-teacher.html', function () {
    return response()->file(public_path('add-teacher.html'));
});

// Öğretmen düzenleme sayfası
Route::get('/edit-teacher.html', function () {
    return response()->file(public_path('edit-teacher.html'));
});

// Öğretmen saatleri sayfası
Route::get('/teacher-schedule.html', function () {
    return response()->file(public_path('teacher-schedule.html'));
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
