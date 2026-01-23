<?php

use App\Http\Controllers\GeneralController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GeneralController::class, 'index']);
Route::get('/blog', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/documentation', [GeneralController::class, 'documentation'])->name('documentation');

Route::prefix('pages')->group(function () {
    Route::get('/sample', [GeneralController::class, 'sample'])->name('sample');
    Route::get('/blog', [BlogController::class, 'blog'])->name('blog');
});

Route::prefix('api')->group(function () {
    // User routes
    Route::get('/users', [GeneralController::class, 'users'])->name('api.users.index');
    Route::post('/users', [GeneralController::class, 'store'])->name('api.users.store');
    Route::get('/users/{identifier}', [GeneralController::class, 'getUser'])->name('api.users.show');
    Route::put('/users/{identifier}', [GeneralController::class, 'update'])->name('api.users.update');
    Route::delete('/users/{identifier}', [GeneralController::class, 'destroy'])->name('api.users.destroy');
    Route::get('/users-master-data', [GeneralController::class, 'masterData'])->name('api.users.master-data');

    // Blog routes
    Route::get('/blogs', [BlogController::class, 'list'])->name('api.blogs.index');
    Route::get('/blogs-list', [BlogController::class, 'lists'])->name('api.blogs.lists');
    Route::post('/blogs', [BlogController::class, 'store'])->name('api.blogs.store');
    Route::get('/blogs/stats', [BlogController::class, 'stats'])->name('api.blogs.stats');
    Route::get('/blogs/{id}', [BlogController::class, 'show'])->name('api.blogs.show');
    Route::put('/blogs/{id}', [BlogController::class, 'update'])->name('api.blogs.update');
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy'])->name('api.blogs.destroy');
    Route::post('/blogs/{id}/view', [BlogController::class, 'incrementView'])->name('api.blogs.view');
    Route::get('/blogs-master-data', [BlogController::class, 'masterData'])->name('api.blogs.master-data');

    // File upload routes
    Route::post('/upload/image', [BlogController::class, 'uploadImage'])->name('api.upload.image');
    Route::post('/upload/video', [BlogController::class, 'uploadVideo'])->name('api.upload.video');
    Route::post('/upload/document', [BlogController::class, 'uploadDocument'])->name('api.upload.document');
    Route::post('/upload/audio', [BlogController::class, 'uploadAudio'])->name('api.upload.audio');
    Route::post('/upload/attachment', [BlogController::class, 'uploadAttachment'])->name('api.upload.attachment');
    Route::post('/upload', [BlogController::class, 'upload'])->name('api.upload.generic');
    Route::delete('/files/{path}', [BlogController::class, 'deleteFile'])->name('api.files.delete')->where('path', '.*');
});

Route::prefix('layouts')->group(function () {
    Route::get('/sample/{type}/{component}', [GeneralController::class, 'getComponentSection'])->name('layouts.user-layout.show');
    Route::get('/blog/{type}/{component}', [BlogController::class, 'getComponentSection'])->name('layouts.blog-layout.show');
});
