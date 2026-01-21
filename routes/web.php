<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout')->middleware('auth');

// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.adminPanel');
    Route::get('/admin/{user}/edit', [AdminController::class, 'edit'])->name('admin.edit');
    Route::put('/admin/{user}', [AdminController::class, 'update'])->name('admin.update');
    Route::delete('/admin/{user}', [AdminController::class, 'destroy'])->name('admin.destroy');
    Route::post('/admin/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.reset-password');

    // Admin category management
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

// Public forum routes (guests can view)
Route::get('/forum', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/forum/search', [CategoryController::class, 'search'])->name('forum.search');
Route::get('/forum/category/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/forum/topic/{topic}', [TopicController::class, 'show'])->name('topics.show');
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

// Global Chat API (public for reading)
Route::get('/chat/messages', [ChatController::class, 'getMessages'])->name('chat.messages');

// Attachments (public access for viewing)
Route::get('/attachments/{attachment}', [AttachmentController::class, 'show'])->name('attachments.show');
Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');

// Forum routes (authenticated users only - interactions)
Route::middleware(['auth'])->group(function () {
    // Topics - create, edit, delete
    Route::get('/forum/category/{category}/new-topic', [TopicController::class, 'create'])->name('topics.create');
    Route::post('/forum/category/{category}/new-topic', [TopicController::class, 'store'])->name('topics.store');
    Route::get('/forum/topic/{topic}/edit', [TopicController::class, 'edit'])->name('topics.edit');
    Route::put('/forum/topic/{topic}', [TopicController::class, 'update'])->name('topics.update');
    Route::delete('/forum/topic/{topic}', [TopicController::class, 'destroy'])->name('topics.destroy');

    // Posts
    Route::post('/forum/topic/{topic}/reply', [PostController::class, 'store'])->name('posts.store');
    Route::get('/forum/post/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/forum/post/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/forum/post/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // User Profile voting
    Route::post('/profile/{user}/vote', [ProfileController::class, 'vote'])->name('profile.vote');

    // Messages
    Route::get('/messages', [MessageController::class, 'inbox'])->name('messages.inbox');
    Route::get('/messages/sent', [MessageController::class, 'sent'])->name('messages.sent');
    Route::get('/messages/compose/{user?}', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

    // User search for autocomplete
    Route::get('/users/search', [MessageController::class, 'searchUsers'])->name('users.search');

    // Global Chat - posting and deleting
    Route::post('/chat/messages', [ChatController::class, 'store'])->name('chat.store');
    Route::delete('/chat/messages/{chatMessage}', [ChatController::class, 'destroy'])->name('chat.destroy');
});
