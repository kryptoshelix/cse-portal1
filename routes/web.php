<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AchievementController as AdminAchievementController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\DirectoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DocumentDownloadController;
use App\Http\Controllers\Faculty\DashboardController as FacultyDashboard;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC SITE  (published content only — enforced in controllers)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'submitContact'])->middleware('throttle:10,1')->name('contact.submit');

Route::get('/achievements', [CatalogController::class, 'achievements'])->name('public.achievements');
Route::get('/achievements/{achievement}', [CatalogController::class, 'achievement'])
    ->whereNumber('achievement')->name('public.achievements.show');
Route::get('/activities', [CatalogController::class, 'activities'])->name('public.activities');
Route::get('/activities/{slug}', [CatalogController::class, 'activity'])->name('public.activities.show');
Route::get('/projects', [CatalogController::class, 'projects'])->name('public.projects');
Route::get('/projects/{id}', [CatalogController::class, 'project'])->whereNumber('id')->name('public.projects.show');
Route::get('/publications', [CatalogController::class, 'publications'])->name('public.publications');
Route::get('/patents', [CatalogController::class, 'patents'])->name('public.patents');
Route::get('/faculty-directory', [CatalogController::class, 'facultyIndex'])->name('public.faculty');
Route::get('/students', [CatalogController::class, 'students'])->name('public.students');
Route::get('/gallery', [CatalogController::class, 'gallery'])->name('public.gallery');
Route::get('/news', [CatalogController::class, 'news'])->name('public.news');
Route::get('/news/{slug}', [CatalogController::class, 'newsShow'])->name('public.news.show');

/*
|--------------------------------------------------------------------------
| GUEST AUTH
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:10,1')->name('register.submit');

    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequest'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->middleware('throttle:6,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:6,1')->name('password.update');
});

// Authenticated-but-not-active notice page + logout (available to all signed-in users).
Route::middleware('auth')->group(function () {
    Route::get('/account/pending', [RegisterController::class, 'pendingNotice'])->name('account.pending');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| STUDENT PORTAL  (role allowlist: student ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
    Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
    Route::get('/achievements/create', [AchievementController::class, 'create'])->name('achievements.create');
    Route::post('/achievements', [AchievementController::class, 'store'])->name('achievements.store');
    Route::get('/achievements/{achievement}', [AchievementController::class, 'show'])->whereNumber('achievement')->name('achievements.show');
    Route::get('/achievements/{achievement}/edit', [AchievementController::class, 'edit'])->whereNumber('achievement')->name('achievements.edit');
    Route::put('/achievements/{achievement}', [AchievementController::class, 'update'])->whereNumber('achievement')->name('achievements.update');
    Route::post('/achievements/{achievement}/submit', [AchievementController::class, 'submitForReview'])->whereNumber('achievement')->name('achievements.submit');
    Route::delete('/achievements/{achievement}', [AchievementController::class, 'destroy'])->whereNumber('achievement')->name('achievements.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'changePassword'])->name('password.change');
});

/*
|--------------------------------------------------------------------------
| FACULTY PORTAL  (role allowlist: faculty ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:faculty'])->prefix('faculty')->name('faculty.')->group(function () {
    Route::get('/dashboard', [FacultyDashboard::class, 'index'])->name('dashboard');
    Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');
    Route::get('/achievements/create', [AchievementController::class, 'create'])->name('achievements.create');
    Route::post('/achievements', [AchievementController::class, 'store'])->name('achievements.store');
    Route::get('/achievements/{achievement}', [AchievementController::class, 'show'])->whereNumber('achievement')->name('achievements.show');
    Route::get('/achievements/{achievement}/edit', [AchievementController::class, 'edit'])->whereNumber('achievement')->name('achievements.edit');
    Route::put('/achievements/{achievement}', [AchievementController::class, 'update'])->whereNumber('achievement')->name('achievements.update');
    Route::post('/achievements/{achievement}/submit', [AchievementController::class, 'submitForReview'])->whereNumber('achievement')->name('achievements.submit');
    Route::delete('/achievements/{achievement}', [AchievementController::class, 'destroy'])->whereNumber('achievement')->name('achievements.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'changePassword'])->name('password.change');
});

/*
|--------------------------------------------------------------------------
| PRIVATE EVIDENCE DOWNLOADS (any authenticated user with record permission)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active'])->get('/documents/{document}/download', [DocumentDownloadController::class, 'download'])
    ->whereNumber('document')->name('documents.download');

/*
|--------------------------------------------------------------------------
| ADMIN AREA  (role allowlist: dept_admin, super_admin ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'active', 'role:dept_admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Achievement categories
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Achievement submissions & review workflow
    Route::get('achievements/pending-review', [AdminAchievementController::class, 'index'])->name('achievements.pending');
    Route::resource('achievements', AdminAchievementController::class);
    Route::post('achievements/{achievement}/review', [AdminAchievementController::class, 'review'])->name('achievements.review');
    Route::post('achievements/{achievement}/publish', [AdminAchievementController::class, 'publish'])->name('achievements.publish');
    Route::post('achievements/{achievement}/unpublish', [AdminAchievementController::class, 'unpublish'])->name('achievements.unpublish');

    // Directories
    Route::get('students', [DirectoryController::class, 'students'])->name('students.index');
    Route::get('students/create', [DirectoryController::class, 'createStudent'])->name('students.create');
    Route::post('students', [DirectoryController::class, 'storeStudent'])->name('students.store');
    Route::put('students/{student}', [DirectoryController::class, 'updateStudent'])->name('students.update');
    Route::get('faculty', [DirectoryController::class, 'facultyIndex'])->name('faculty.index');
    Route::put('faculty/{faculty}', [DirectoryController::class, 'updateFaculty'])->name('faculty.update');

    // Content modules (generic CRUD + explicit publish actions)
    foreach (['activities', 'projects', 'publications', 'patents', 'gallery', 'news'] as $module) {
        Route::get($module, [ContentController::class, 'index'])->name($module.'.index');
        Route::get($module.'/create', [ContentController::class, 'create'])->name($module.'.create');
        Route::post($module, [ContentController::class, 'store'])->name($module.'.store');
        Route::get($module.'/{id}/edit', [ContentController::class, 'edit'])->whereNumber('id')->name($module.'.edit');
        Route::put($module.'/{id}', [ContentController::class, 'update'])->whereNumber('id')->name($module.'.update');
        Route::post($module.'/{id}/publish', [ContentController::class, 'publish'])->whereNumber('id')->name($module.'.publish');
        Route::post($module.'/{id}/unpublish', [ContentController::class, 'unpublish'])->whereNumber('id')->name($module.'.unpublish');
        Route::delete($module.'/{id}', [ContentController::class, 'destroy'])->whereNumber('id')->name($module.'.destroy');
    }

    // Account approval & management
    Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('accounts/{user}', [AccountController::class, 'show'])->whereNumber('user')->name('accounts.show');
    Route::put('accounts/{user}/status', [AccountController::class, 'setStatus'])->whereNumber('user')->name('accounts.status');
    Route::put('accounts/{user}/role', [AccountController::class, 'setRole'])->whereNumber('user')->name('accounts.role');
    Route::put('accounts/{user}/review-flag', [AccountController::class, 'setReviewFlag'])->whereNumber('user')->name('accounts.review-flag');

    // Audit log viewer
    Route::get('audit-logs', [AccountController::class, 'auditLogs'])->name('audit.index');
});
