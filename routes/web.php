<?php

use App\Http\Controllers\GeneralController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ListingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GeneralController::class, 'index']);
Route::get('/blog', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/listing', [ListingController::class, 'index'])->name('listings.index');
Route::get('/documentation', [GeneralController::class, 'documentation'])->name('documentation');

Route::prefix('pages')->middleware('cache.headers:layout')->group(function () {
    Route::get('/sample', [GeneralController::class, 'sample'])->name('sample');
    Route::get('/blog', [BlogController::class, 'blog'])->name('blog');
    Route::get('/listing', [ListingController::class, 'listing'])->name('listing');
});

Route::prefix('api')->group(function () {
    // User routes
    Route::get('/users', [GeneralController::class, 'users'])->name('api.users.index');
    Route::post('/users', [GeneralController::class, 'store'])->name('api.users.store');
    Route::get('/users/{identifier}', [GeneralController::class, 'getUser'])->name('api.users.show');
    Route::put('/users/{identifier}', [GeneralController::class, 'update'])->name('api.users.update');
    Route::delete('/users/{identifier}', [GeneralController::class, 'destroy'])->name('api.users.destroy');
    Route::get('/users-master-data', [GeneralController::class, 'masterData'])->middleware('cache.headers:static')->name('api.users.master-data');

    // Blog routes - using route model binding
    Route::get('/blogs', [BlogController::class, 'list'])->name('api.blogs.index');
    Route::get('/blogs-list', [BlogController::class, 'lists'])->name('api.blogs.lists');
    Route::post('/blogs', [BlogController::class, 'store'])->name('api.blogs.store');
    Route::get('/blogs/stats', [BlogController::class, 'stats'])->middleware('cache.headers:api')->name('api.blogs.stats');
    Route::get('/blogs/{blog}', [BlogController::class, 'show'])->name('api.blogs.show');
    Route::put('/blogs/{blog}', [BlogController::class, 'update'])->name('api.blogs.update');
    Route::delete('/blogs/{blog}', [BlogController::class, 'destroy'])->name('api.blogs.destroy');
    Route::post('/blogs/{blog}/view', [BlogController::class, 'incrementView'])->name('api.blogs.view');
    Route::get('/blogs-master-data', [BlogController::class, 'masterData'])->middleware('cache.headers:static')->name('api.blogs.master-data');

    // File upload routes
    Route::post('/upload/image', [BlogController::class, 'uploadImage'])->name('api.upload.image');
    Route::post('/upload/video', [BlogController::class, 'uploadVideo'])->name('api.upload.video');
    Route::post('/upload/document', [BlogController::class, 'uploadDocument'])->name('api.upload.document');
    Route::post('/upload/audio', [BlogController::class, 'uploadAudio'])->name('api.upload.audio');
    Route::post('/upload/attachment', [BlogController::class, 'uploadAttachment'])->name('api.upload.attachment');
    Route::post('/upload', [BlogController::class, 'upload'])->name('api.upload.generic');
    Route::delete('/files/{path}', [BlogController::class, 'deleteFile'])->name('api.files.delete')->where('path', '.*');

    // Listing routes - using route model binding
    Route::get('/listing', [ListingController::class, 'list'])->name('api.listings.index');
    Route::post('/listing', [ListingController::class, 'create'])->name('api.listings.store');
    Route::get('/listing-master-data', [ListingController::class, 'getMasterDataApi'])->middleware('cache.headers:static')->name('api.listings.master-data');
    Route::get('/listing/stats/{id}', [ListingController::class, 'getStats'])->middleware('cache.headers:api')->name('api.listings.stats');
    Route::get('/listing/{listing}', [ListingController::class, 'show'])->name('api.listings.show');
    Route::put('/listing/{listing}', [ListingController::class, 'update'])->name('api.listings.update');
    Route::delete('/listing/{listing}', [ListingController::class, 'delete'])->name('api.listings.destroy');

    // Listing file upload routes
    Route::post('/listing-upload/image', [ListingController::class, 'uploadImage'])->name('api.listing-upload.image');
    Route::post('/listing-upload/document', [ListingController::class, 'uploadDocument'])->name('api.listing-upload.document');
    Route::post('/listing-upload/video', [ListingController::class, 'uploadVideo'])->name('api.listing-upload.video');
    Route::post('/listing-upload/attachment', [ListingController::class, 'uploadAttachment'])->name('api.listing-upload.attachment');
    Route::post('/listing-upload', [ListingController::class, 'upload'])->name('api.listing-upload.generic');
    Route::delete('/listing-files/{path}', [ListingController::class, 'deleteFile'])->name('api.listing-files.delete')->where('path', '.*');
});

Route::prefix('layouts')->middleware('cache.headers:layout')->group(function () {
    Route::get('/sample/{type}/{component}', [GeneralController::class, 'getComponentSection'])->name('layouts.user-layout.show');
    Route::get('/blog/{type}/{component}', [BlogController::class, 'getComponentSection'])->name('layouts.blog-layout.show');
    Route::get('/listing/{type}/{component}', [ListingController::class, 'getComponentSection'])->name('layouts.listing-layout.show');
});
