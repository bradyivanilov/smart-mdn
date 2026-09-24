<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Root redirect
$routes->get('/', 'AuthController::login');

// Auth routes (Supabase Auth)
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::processLogin');
$routes->get('register', 'AuthController::register');
$routes->post('register', 'AuthController::processRegister');
$routes->get('logout', 'AuthController::logout');

// Public Feature: e-CV Portofolio Publik Guru
$routes->get('portfolio/(:segment)', 'DashboardController::publicPortfolio/$1');

// Protected Routes (General Authenticated)
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Shared: Profil Pengguna & Repositori Karya
    $routes->get('profile', 'DashboardController::profile');
    $routes->get('creativity', 'CreativityController::index');
    $routes->get('creativity/create', 'CreativityController::create');
    $routes->post('creativity/store', 'CreativityController::store');
    $routes->post('creativity/like/(:num)', 'CreativityController::like/$1');
});

// Protected: Khusus Guru
$routes->group('', ['filter' => ['auth', 'role:guru']], function($routes) {
    // Dashboard Guru
    $routes->get('dashboard', 'DashboardController::index');

    // Smart Attendance Guru (GPS + Kamera)
    $routes->get('attendance', 'AttendanceController::index');
    $routes->post('attendance/record', 'AttendanceController::record');

    // Our Activity (Jurnal 4 Kompetensi Guru)
    $routes->get('activity', 'ActivityController::index');
    $routes->get('activity/create', 'ActivityController::create');
    $routes->post('activity/store', 'ActivityController::store');

    // Our Refleksi (Kemendikdasmen Framework 4 Level)
    $routes->get('reflection', 'ReflectionController::index');
    $routes->get('reflection/create', 'ReflectionController::create');
    $routes->post('reflection/store', 'ReflectionController::store');
});

// Protected: Khusus Kepala Sekolah / Supervisor
$routes->group('', ['filter' => ['auth', 'role:kepala_sekolah']], function($routes) {
    // Portal Supervisi & Manajemen Dewan Guru
    $routes->get('supervisor', 'SupervisorController::index');
    $routes->get('supervisor/teachers', 'SupervisorController::teachers');
    $routes->get('supervisor/teacher/(:segment)', 'SupervisorController::teacherDetail/$1');

    // Asesmen Observasi KBM Klinis
    $routes->get('supervisor/supervise/(:segment)', 'SupervisorController::createSupervision/$1');
    $routes->post('supervisor/store-supervision', 'SupervisorController::storeSupervision');

    // Verifikasi Jurnal KBM & Umpan Balik Refleksi
    $routes->post('supervisor/verify-activity/(:num)', 'SupervisorController::verifyActivity/$1');
    $routes->post('supervisor/feedback-reflection/(:num)', 'SupervisorController::feedbackReflection/$1');

    // Kurasi Karya & Modul Ajar Guru
    $routes->post('supervisor/curate-creativity/(:num)', 'SupervisorController::curateCreativity/$1');
});
