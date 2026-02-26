<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProductPropertyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GeneralController::class, 'index']);
Route::get('/blog', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/listing', [ListingController::class, 'index'])->name('listings.index');
Route::get('/product-properties', [ProductPropertyController::class, 'index'])->name('product-properties.index');
Route::get('/documentation', [GeneralController::class, 'documentation'])->name('documentation');

Route::prefix('pages')->middleware('cache.headers:layout')->group(function () {
    Route::get('/sample', [GeneralController::class, 'sample'])->name('sample');
    Route::get('/blog', [BlogController::class, 'blog'])->name('blog');
    Route::get('/listing', [ListingController::class, 'listing'])->name('listing');
    Route::get('/product-property', [ProductPropertyController::class, 'listing'])->name('product-property');
});

Route::prefix('api')->group(function () {
    // User routes
    Route::get('/users', [GeneralController::class, 'users'])->name('api.users.index');
    Route::post('/users', [GeneralController::class, 'store'])->name('api.users.store');
    Route::get('/users/{identifier}', [GeneralController::class, 'getUser'])->name('api.users.show');
    Route::put('/users/{identifier}', [GeneralController::class, 'update'])->name('api.users.update');
    Route::delete('/users/{identifier}', [GeneralController::class, 'destroy'])->name('api.users.destroy');
    Route::get('/users-master-data', [GeneralController::class, 'masterData'])->middleware('cache.headers:static')->name('api.users.master-data');

    // User file upload routes
    Route::post('/user-upload/image', [GeneralController::class, 'uploadImage'])->name('api.user-upload.image');
    Route::post('/user-upload/document', [GeneralController::class, 'uploadDocument'])->name('api.user-upload.document');
    Route::post('/user-upload', [GeneralController::class, 'upload'])->name('api.user-upload.generic');
    Route::delete('/user-files/{path}', [GeneralController::class, 'deleteFile'])->name('api.user-files.delete')->where('path', '.*');

    // Blog routes - using route model binding
    Route::get('/blogs', [BlogController::class, 'list'])->name('api.blogs.index');
    Route::get('/blogs-list', [BlogController::class, 'lists'])->name('api.blogs.lists');
    Route::post('/blogs', [BlogController::class, 'store'])->name('api.blogs.store');
    Route::get('/blogs/stats/{id}', [BlogController::class, 'getStats'])->middleware('cache.headers:api')->name('api.blogs.stats');
    Route::get('/blogs/{blog}', [BlogController::class, 'show'])->name('api.blogs.show');
    Route::put('/blogs/{blog}', [BlogController::class, 'update'])->name('api.blogs.update');
    Route::delete('/blogs/{blog}', [BlogController::class, 'destroy'])->name('api.blogs.destroy');
    Route::post('/blogs/{blog}/view', [BlogController::class, 'incrementView'])->name('api.blogs.view');
    Route::get('/blogs-master-data', [BlogController::class, 'masterData'])->middleware('cache.headers:static')->name('api.blogs.master-data');

    // Blog file upload routes
    Route::post('/blog-upload/image', [BlogController::class, 'uploadImage'])->name('api.blog-upload.image');
    Route::post('/blog-upload/video', [BlogController::class, 'uploadVideo'])->name('api.blog-upload.video');
    Route::post('/blog-upload/document', [BlogController::class, 'uploadDocument'])->name('api.blog-upload.document');
    Route::post('/blog-upload/audio', [BlogController::class, 'uploadAudio'])->name('api.blog-upload.audio');
    Route::post('/blog-upload/attachment', [BlogController::class, 'uploadAttachment'])->name('api.blog-upload.attachment');
    Route::post('/blog-upload', [BlogController::class, 'upload'])->name('api.blog-upload.generic');
    Route::delete('/blog-files/{path}', [BlogController::class, 'deleteFile'])->name('api.blog-files.delete')->where('path', '.*');

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

    // Product Property routes - using route model binding
    Route::get('/product-property', [ProductPropertyController::class, 'list'])->name('api.product-properties.index');
    Route::post('/product-property', [ProductPropertyController::class, 'create'])->name('api.product-properties.store');
    Route::get('/product-property-master-data', [ProductPropertyController::class, 'getMasterDataApi'])->middleware('cache.headers:static')->name('api.product-properties.master-data');
    Route::get('/product-property/stats/{id}', [ProductPropertyController::class, 'getStats'])->middleware('cache.headers:api')->name('api.product-properties.stats');
    Route::get('/product-property/{property}', [ProductPropertyController::class, 'show'])->name('api.product-properties.show');
    Route::put('/product-property/{property}', [ProductPropertyController::class, 'update'])->name('api.product-properties.update');
    Route::delete('/product-property/{property}', [ProductPropertyController::class, 'delete'])->name('api.product-properties.destroy');
    Route::get('/product-property/{property}/activities', [ProductPropertyController::class, 'activities'])->name('api.product-properties.activities');
    Route::get('/product-property/{property}/followups', [ProductPropertyController::class, 'listFollowUps'])->name('api.product-properties.followups.index');
    Route::post('/product-property/{property}/followups', [ProductPropertyController::class, 'createFollowUp'])->name('api.product-properties.followups.store');
    Route::get('/product-property/{property}/followups/{followupEid}', [ProductPropertyController::class, 'showFollowUp'])->name('api.product-properties.followups.show');
    Route::put('/product-property/{property}/followups/{followupEid}', [ProductPropertyController::class, 'updateFollowUp'])->name('api.product-properties.followups.update');
    Route::delete('/product-property/{property}/followups/{followupEid}', [ProductPropertyController::class, 'deleteFollowUp'])->name('api.product-properties.followups.destroy');
    Route::get('/product-property/{property}/notes', [ProductPropertyController::class, 'listNotes'])->name('api.product-properties.notes.index');
    Route::post('/product-property/{property}/notes', [ProductPropertyController::class, 'createNote'])->name('api.product-properties.notes.store');
    Route::put('/product-property/{property}/notes/{noteEid}', [ProductPropertyController::class, 'updateNote'])->name('api.product-properties.notes.update');
    Route::delete('/product-property/{property}/notes/{noteEid}', [ProductPropertyController::class, 'deleteNote'])->name('api.product-properties.notes.destroy');

    // Product Property file upload routes
    Route::post('/product-property-upload/image', [ProductPropertyController::class, 'uploadImage'])->name('api.product-property-upload.image');
    Route::post('/product-property-upload/document', [ProductPropertyController::class, 'uploadDocument'])->name('api.product-property-upload.document');
    Route::post('/product-property-upload/video', [ProductPropertyController::class, 'uploadVideo'])->name('api.product-property-upload.video');
    Route::post('/product-property-upload/attachment', [ProductPropertyController::class, 'uploadAttachment'])->name('api.product-property-upload.attachment');
    Route::post('/product-property-upload', [ProductPropertyController::class, 'upload'])->name('api.product-property-upload.generic');
    Route::delete('/product-property-files/{path}', [ProductPropertyController::class, 'deleteFile'])->name('api.product-property-files.delete')->where('path', '.*');

    // Common Image API - Serve images from storage or public with CORS support
    Route::match(['get', 'options'], '/images/{path}', [ImageController::class, 'show'])->name('images.show')->where('path', '.*');
    Route::match(['get', 'options'], '/images/thumbnail/{path}', [ImageController::class, 'thumbnail'])->name('images.thumbnail')->where('path', '.*');
    Route::match(['post', 'options'], '/images/generate-url', [ImageController::class, 'generateUrl'])->name('images.generate-url');
});

Route::prefix('layouts')->middleware('cache.headers:layout')->group(function () {
    Route::get('/sample/{type}/{component}', [GeneralController::class, 'getComponentSection'])->name('layouts.user-layout.show');
    Route::get('/blog/{type}/{component}', [BlogController::class, 'getComponentSection'])->name('layouts.blog-layout.show');
    Route::get('/listing/{type}/{component}', [ListingController::class, 'getComponentSection'])->name('layouts.listing-layout.show');
    Route::get('/product-property/{type}/{component}', [ProductPropertyController::class, 'getComponentSection'])->name('layouts.product-property-layout.show');
});
