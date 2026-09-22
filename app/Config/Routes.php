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

// Public Features (QR Student Pulse & e-CV Portfolio Guru)
$routes->get('pulse/(:segment)', 'ReflectionController::studentPulse/$1');
$routes->post('pulse/store', 'ReflectionController::storeStudentPulse');
$routes->get('portfolio/(:segment)', 'DashboardController::publicPortfolio/$1');

// Protected Routes (Guru & Kepala Sekolah)
$routes->group('', ['filter' => 'auth'], function($routes) {
    // Dashboard & Profile
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('profile', 'DashboardController::profile');

    // Smart Attendance (Khusus Guru)
    $routes->get('attendance', 'AttendanceController::index');
    $routes->post('attendance/record', 'AttendanceController::record');

    // Our Activity (Jurnal 4 Kompetensi)
    $routes->get('activity', 'ActivityController::index');
    $routes->get('activity/create', 'ActivityController::create');
    $routes->post('activity/store', 'ActivityController::store');

    // Our Creativity (Karya & Modul Ajar)
    $routes->get('creativity', 'CreativityController::index');
    $routes->get('creativity/create', 'CreativityController::create');
    $routes->post('creativity/store', 'CreativityController::store');
    $routes->post('creativity/like/(:num)', 'CreativityController::like/$1');

    // Our Refleksi (Kemendikdasmen Framework)
    $routes->get('reflection', 'ReflectionController::index');
    $routes->get('reflection/create', 'ReflectionController::create');
    $routes->post('reflection/store', 'ReflectionController::store');

    // Portal Kepala Sekolah / Supervisor
    $routes->get('supervisor', 'SupervisorController::index');
    $routes->get('supervisor/teachers', 'SupervisorController::teachers');
    $routes->get('supervisor/supervise/(:segment)', 'SupervisorController::createSupervision/$1');
    $routes->post('supervisor/store-supervision', 'SupervisorController::storeSupervision');
    $routes->post('supervisor/verify-activity/(:num)', 'SupervisorController::verifyActivity/$1');
    $routes->post('supervisor/feedback-reflection/(:num)', 'SupervisorController::feedbackReflection/$1');
});
