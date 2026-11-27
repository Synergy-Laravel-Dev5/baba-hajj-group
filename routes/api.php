<?php

use App\Http\Controllers\Api\AppAuthController;
use App\Http\Controllers\Api\AppBookingController;
use App\Http\Controllers\Api\AppChatController;
use App\Http\Controllers\Api\AppContentController;
use App\Http\Controllers\Api\AppNotificationController;
use App\Http\Controllers\Api\AppPackageController;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\TripController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public Endpoint
Route::post('/login', [CustomerAuthController::class, 'login']);

// Protected Endpoints (Requires Bearer Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [CustomerAuthController::class, 'me']);
    Route::post('/logout', [CustomerAuthController::class, 'logout']);
    Route::get('/booking/details', [TripController::class, 'getBookingDetails']);
    Route::get('/booking/package', [TripController::class, 'getPackageDetails']);

    Route::post('/documents/{personId}', [DocumentController::class, 'upload']);
    Route::get('/documents/{personId}', [DocumentController::class, 'show']);
});

Route::prefix('guest')->group(function () {
    Route::get('/packages', [GuestController::class, 'getPackages']);
    Route::get('/packages/{id}', [GuestController::class, 'getPackageById']);
    Route::post('/inquiry', [GuestController::class, 'submitInquiry']);
    Route::get('/gallery', [GuestController::class, 'getGalleries']);
    Route::get('/gallery/{id}', [GuestController::class, 'getGalleryDetail']);
    Route::get('/books', [GuestController::class, 'getHajjBooks']);
    Route::get('/books/{id}', [GuestController::class, 'getHajjBookDetail']);
    Route::get('/guidelines', [GuestController::class, 'getHujjajGuidelines']);
    Route::get('/guidelines/{id}', [GuestController::class, 'getHujjajGuidelineDetail']);
    Route::get('/videos', [GuestController::class, 'getHajjVideos']);
    Route::get('/videos/{id}', [GuestController::class, 'getHajjVideoDetail']);
    Route::get('/visa-countries', [GuestController::class, 'getVisaCountries']);
    Route::get('/visa-countries/{id}', [GuestController::class, 'getVisaCountryDetail']);
    Route::post('/visa-inquiry', [GuestController::class, 'submitVisaInquiry']);
});

/*
|--------------------------------------------------------------------------
| Mobile App API Endpoints (/api/app/...)
|--------------------------------------------------------------------------
| Designed specifically for the Baba Hajj & Umrah Mobile App.
| Supports Authentication, Hajj/Umrah Packages, Booking Inquiries,
| Hajj Guidelines, Video Tutorials (Live & Library), Books, Ziyarat, Duas,
| In-App Support Chat, and Notifications.
*/
Route::prefix('app')->group(function () {
    // 1. Authentication (Public)
    Route::post('/auth/register', [AppAuthController::class, 'register']);
    Route::post('/auth/login', [AppAuthController::class, 'login']);

    // 2. Packages (Public)
    Route::get('/packages', [AppPackageController::class, 'index']);
    Route::get('/packages/hajj', [AppPackageController::class, 'hajj']);
    Route::get('/packages/umrah', [AppPackageController::class, 'umrah']);
    Route::get('/packages/{id}', [AppPackageController::class, 'show']);

    // 3. Content, Guidelines, Videos, Books, Ziyarat & Duas (Public)
    Route::get('/guides', [AppContentController::class, 'guides']);
    Route::get('/guides/{id}', [AppContentController::class, 'guideDetail']);
    Route::get('/videos', [AppContentController::class, 'videos']);
    Route::get('/videos/{id}', [AppContentController::class, 'videoDetail']);
    Route::get('/books', [AppContentController::class, 'books']);
    Route::get('/books/{id}', [AppContentController::class, 'bookDetail']);
    Route::get('/ziyarat', [AppContentController::class, 'ziyarat']);
    Route::get('/ziyarat/{id}', [AppContentController::class, 'ziyaratDetail']);
    Route::get('/duas', [AppContentController::class, 'duas']);
    Route::get('/emergency-contacts', [AppContentController::class, 'emergencyContacts']);

    // 4. Booking Request Submission (Public: works for both guest and authenticated users)
    Route::post('/bookings', [AppBookingController::class, 'store']);

    // 5. Protected Endpoints for Authenticated Mobile App User (Bearer Token)
    Route::middleware('auth:sanctum')->group(function () {
        // User Profile & Password
        Route::get('/auth/profile', [AppAuthController::class, 'profile']);
        Route::post('/auth/profile', [AppAuthController::class, 'updateProfile']);
        Route::post('/auth/change-password', [AppAuthController::class, 'changePassword']);
        Route::post('/auth/logout', [AppAuthController::class, 'logout']);

        // User Bookings History & Status
        Route::get('/bookings/my', [AppBookingController::class, 'myBookings']);
        Route::get('/bookings/{id}', [AppBookingController::class, 'show']);

        // Live In-App Chat with Support
        Route::get('/chat/messages', [AppChatController::class, 'getMessages']);
        Route::post('/chat/send', [AppChatController::class, 'sendMessage']);
        Route::post('/chat/read', [AppChatController::class, 'markAsRead']);

        // User In-App Notifications
        Route::get('/notifications', [AppNotificationController::class, 'index']);
        Route::post('/notifications/read-all', [AppNotificationController::class, 'markAllAsRead']);
        Route::post('/notifications/{id}/read', [AppNotificationController::class, 'markAsRead']);
    });
});
