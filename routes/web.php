<?php

use App\Http\Controllers\Admin\ClassRoomController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminLab\TAController;
use App\Http\Controllers\AdminLab\LabScheduleController;
use App\Http\Controllers\AdminLab\LabClassController;
use App\Http\Controllers\ClassDetailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SyllabusController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/accessibility/toggle', [ProfileController::class, 'toggleAccessibility'])->name('profile.accessibility.toggle');

    // Schedule
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');

    // Class Detail
    Route::get('/class/{classroom}', [ClassDetailController::class, 'show'])->name('class.show');
    Route::get('/class/{classroom}/people', [ClassDetailController::class, 'people'])->name('class.people');
    Route::get('/class/{classroom}/session/{courseSession}', [ClassDetailController::class, 'session'])->name('class.session');

    // Modules
    Route::post('/class/{classroom}/session/{session}/module', [ModuleController::class, 'store'])->name('module.store');
    Route::get('/module/{module}/download', [ModuleController::class, 'download'])->name('module.download');
    Route::delete('/module/{module}', [ModuleController::class, 'destroy'])->name('module.destroy');

    // Forum
    Route::get('/class/{classroom}/forum', [ForumController::class, 'index'])->name('forum.index');
    Route::get('/class/{classroom}/forum/create', [ForumController::class, 'create'])->name('forum.create');
    Route::post('/class/{classroom}/forum', [ForumController::class, 'store'])->name('forum.store');
    Route::delete('/forum/{post}', [ForumController::class, 'destroy'])->name('forum.destroy');

    // Gradebook
    Route::get('/class/{classroom}/gradebook', [GradeController::class, 'index'])->name('gradebook.index');
    Route::post('/class/{classroom}/grade', [GradeController::class, 'store'])->name('grade.store');
    Route::post('/class/{classroom}/grade/rubric', [GradeController::class, 'storeRubric'])->name('grade.rubric.store');
    Route::delete('/grade/{grade}', [GradeController::class, 'destroy'])->name('grade.destroy');

    // Syllabus
    Route::get('/class/{classroom}/syllabus', [SyllabusController::class, 'show'])->name('syllabus.show');
    Route::post('/class/{classroom}/syllabus', [SyllabusController::class, 'store'])->name('syllabus.store');

    // Notifications mark-read
    Route::post('/notifications/mark-read', [\App\Http\Controllers\PushNotificationController::class, 'markRead'])->name('notifications.markRead');

    // Session Activities (Allow Admin & Lecturer)
    Route::post('/sessions/{session}/activities', [SessionController::class, 'storeActivity'])->name('session_activities.store');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('courses', CourseController::class);
    Route::post('courses/{course}/sessions', [CourseController::class, 'storeSession'])->name('courses.sessions.store');
    Route::put('courses/{course}/sessions/{session}', [CourseController::class, 'updateSession'])->name('courses.sessions.update');
    Route::delete('courses/{course}/sessions/{session}', [CourseController::class, 'destroySession'])->name('courses.sessions.destroy');
    Route::resource('classrooms', ClassRoomController::class)->except(['show']);
    Route::resource('schedules', AdminScheduleController::class)->except(['show']);

    // Sessions within a classroom
    Route::get('/classrooms/{classroom}/sessions', [SessionController::class, 'index'])->name('classrooms.sessions.index');
    Route::get('/classrooms/{classroom}/sessions/create', [SessionController::class, 'create'])->name('classrooms.sessions.create');
    Route::post('/classrooms/{classroom}/sessions', [SessionController::class, 'store'])->name('classrooms.sessions.store');
    Route::get('/classrooms/{classroom}/sessions/{session}/edit', [SessionController::class, 'edit'])->name('classrooms.sessions.edit');
    Route::put('/classrooms/{classroom}/sessions/{session}', [SessionController::class, 'update'])->name('classrooms.sessions.update');
    Route::delete('/classrooms/{classroom}/sessions/{session}', [SessionController::class, 'destroy'])->name('classrooms.sessions.destroy');

    // Push Notifications
    Route::get('/notifications/create', [\App\Http\Controllers\PushNotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications', [\App\Http\Controllers\PushNotificationController::class, 'store'])->name('notifications.store');
});

// Admin Laboratory Routes
Route::middleware(['auth', 'admin_lab'])->prefix('admin-lab')->name('admin_lab.')->group(function () {
    // TA Management
    Route::get('/ta', [TAController::class, 'index'])->name('ta.index');
    Route::get('/ta/{user}/promote', [TAController::class, 'promote'])->name('ta.promote');
    Route::post('/ta/{user}/promote', [TAController::class, 'executePromotion'])->name('ta.execute_promotion');
    Route::post('/ta/{user}/demote', [TAController::class, 'demote'])->name('ta.demote');
    Route::get('/ta/{user}/edit-id', [TAController::class, 'editTaId'])->name('ta.edit_id');
    Route::put('/ta/{user}/edit-id', [TAController::class, 'updateTaId'])->name('ta.update_id');

    // LAB Schedules (with conflict detection)
    Route::resource('schedules', LabScheduleController::class)->except(['show']);

    // LAB Classes
    Route::resource('classes', LabClassController::class)->except(['show'])->parameters(['classes' => 'classroom']);
});

require __DIR__.'/auth.php';

