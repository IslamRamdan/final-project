<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\UserAuth;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\OffersController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\ProductsRequestsController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgetPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'check_admin', 'check_block_status'])->group(function () {
    Route::post('/send-custom-offer', [OffersController::class, 'sendCustomOffer']);

    Route::post('/make-offer', [OffersController::class, 'makeOffer']);
    Route::get('/get-offers', [OffersController::class, 'getOffers']);
    Route::get('/get-special-offers', [OffersController::class, 'getSpecialOffers']);
    
    Route::put('/update-offer/{offerId}', [OffersController::class, 'updateOffer']);
Route::put('/update-custom-offer/{offerId}', [OffersController::class, 'updateCustomOffer']);

Route::delete('/delete-offer/{offerId}',[OffersController::class, 'deleteOffer']);


    
    Route::get('/bills', [BillController::class, 'index']);
    Route::get('/bills/{id}', [BillController::class, 'show']);

    Route::post('/create-bill', [BillController::class, 'createBill']);

    Route::get('/total-income/{month}', [BillController::class, 'getTotalIncome']);

    Route::get('booking/total-income/{month}', [BillController::class, 'bookingTotalIncome']);
});




// login admin
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->middleware(['auth:sanctum']);

// forget password
Route::post('password/forget-password', [ForgetPasswordController::class, 'forgetPassword']);
//comparison verification-code
Route::post('password/verification-code', [ResetPasswordController::class, 'verificationCode']);

Route::middleware(['auth:sanctum'])->group(function () {
    // reset password with verification code
    Route::post('password/reset-password', [ResetPasswordController::class, 'resetPassword']);
    // reset password with old password
    Route::post('password/new-password', [ResetPasswordController::class, 'resetPasswordWithOldPassword']);
});



Route::middleware(['auth:sanctum', 'check_block_status'])->group(function () {
    // get sections
    Route::get('/section', [SectionController::class, 'getSections']);
    // get section by id
    Route::get('/section/{id}', [SectionController::class, 'oneSection']);
});
Route::middleware(['auth:sanctum', 'check_admin', 'check_block_status'])->group(function () {
    // add section
    Route::post('/section', [SectionController::class, 'addSection']);
    // update section
    Route::post('/section/{id}', [SectionController::class, 'updateSection']);
    // delete section
    Route::delete('/section/{id}', [SectionController::class, 'deleteSection']);
});



Route::middleware(['auth:sanctum', 'check_block_status'])->group(function () {
    // get categories
    Route::get('/categories', [CategoryController::class, 'categories']);
    // get categories by section id
    Route::get('/{section_id}/categories', [CategoryController::class, 'sectionCategories']);
    // get category by id
    Route::get('/categories/{id}', [CategoryController::class, 'categoryId']);
});
Route::middleware(['auth:sanctum', 'check_admin', 'check_block_status'])->group(function () {
    // add category
    Route::post('/categories', [CategoryController::class, 'addCategory']);
    // update category
    Route::post('categories/{id}', [CategoryController::class, 'updateCategory']);
    // delete category
    Route::delete('categories/{id}', [CategoryController::class, 'deleteCategory']);
});



Route::middleware(['auth:sanctum', 'check_block_status'])->group(function () {
    // get items
    Route::get('/items', [ItemController::class, 'items']);
    // get items by category
    Route::get('/{categoty_id}/items', [ItemController::class, 'itemsByCategory']);
    // get item by id
    Route::get('/items/{id}', [ItemController::class, 'itemById']);
});
Route::middleware(['auth:sanctum', 'check_admin', 'check_block_status'])->group(function () {
    // add item
    Route::post('/items', [ItemController::class, 'addItem']);
    // update item
    Route::post('/items/{id}', [ItemController::class, 'updateItem']);
    // delete item
    Route::delete('/items/{id}', [ItemController::class, 'deleteItem']);
});



Route::middleware(['auth:sanctum', 'check_block_status'])->group(function () {
    // get reviews
    Route::get('/{items}/reviews', [ReviewController::class, 'reviews']);
    // get reviews by id
    Route::get('/reviews/{id}', [ReviewController::class, 'reviewsId']);
    //  add review
    Route::post('/reviews', [ReviewController::class, 'addReview']);
    // update review
    Route::post('/reviews/{id}', [ReviewController::class, 'updateReview']);
    // delete review
    Route::delete('/reviews/{id}', [ReviewController::class, 'deleteReview']);
});




// api users routes
// register (user)
Route::post('/user/register', [UserAuth::class, 'register']);
// send code
Route::post('/user/code', [UserAuth::class, 'sendCode']);
// confirm code
Route::post('/user/verification', [UserAuth::class, 'verificationCode']);
// login (user)
Route::post('/user/login', [UserAuth::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    // edit image and username
    Route::post('/user/edit/{id}', [UserAuth::class, 'editProfile']);
    // edit location
    Route::post('/user/location/{id}', [UserAuth::class, 'editLocation']);
});


Route::middleware(['auth:sanctum', 'check_block_status'])->group(function () {
    // Booking
    Route::get('/user/booking', [BookingController::class, 'bookings']);
    // add booking
    Route::post('/user/booking', [BookingController::class, 'addBooking']);
    // update booking
    Route::post('/user/booking/{id}', [BookingController::class, 'updateBooking']);
    // cancel Booking
    Route::get('/user/booking/cancel/{id}', [BookingController::class, 'cancelBooking']);
    // receive Booking
    Route::get('/user/booking/receive/{id}', [BookingController::class, 'receiveBooking']);
    // get receive Bookings
    Route::get('/booking/receive', [BookingController::class, 'receives']);
    // get cancel Bookings
    Route::get('/booking/cancel', [BookingController::class, 'bookingCanceled']);
    // search bookings by day
    Route::post('/booking/search', [BookingController::class, 'getBookByDay']);
});



Route::middleware(['auth:sanctum', 'check_block_status'])->group(function () {
    // Products requests
    Route::get('/user/requests', [ProductsRequestsController::class, 'requests'])->middleware('check_admin');
    // get request by id
    Route::get('/user/requests/{id}', [ProductsRequestsController::class, 'getReqById']);
    // add request
    Route::post('/user/requests', [ProductsRequestsController::class, 'addRequset']);
    // update request
    Route::post('/user/requests/{id}', [ProductsRequestsController::class, 'updateRequset']);
    // delete request
    Route::delete('/user/requests/{id}', [ProductsRequestsController::class, 'deleteRequset']);
});





// test
Route::post('/register', function () {
    $user = new User;
    $user->name = 'eslam';
    $user->email = 'eslam@gmail.com';
    $user->location = null;
    $user->image = 'default_image.jpg';
    $user->password = bcrypt('12345678');
    $user->save();

    $role = new Role();
    $role->role = 'admin';
    $user->role()->save($role);

    // $user = User::find(3)->role;
    return $user;
});













Route::middleware(['auth:sanctum', 'check_admin'])->group(function () {

    Route::get('/admin/users', [App\Http\Controllers\UserController::class, 'showAllCustomers'])->name('index');/* ->middleware(['auth:sanctum', 'check_admin']); */

    Route::post('/admin/blockuser/{id}', [App\Http\Controllers\UserController::class, 'blockUser'])->name('user.block');/* ->middleware(['auth:sanctum', 'check_admin']); */

    Route::post('/admin/unblockuser/{id}', [App\Http\Controllers\UserController::class, 'unBlockUser'])->name('user.unblock');/* ->middleware(['auth:sanctum', 'check_admin']); */

    Route::get('/admin/showBlocked/', [App\Http\Controllers\UserController::class, 'showBlocked'])->name('user.showBlocked');/* ->middleware(['auth:sanctum', 'check_admin']); */



    Route::get('admin/showAllNotification', [App\Http\Controllers\RequestController::class, 'showAllNotification'])->name('notifications.show');/* ->middleware(['auth:sanctum', 'check_admin']); */

    Route::get('admin/showNotification/{id}', [App\Http\Controllers\RequestController::class, 'showNotification'])->name('notification.show');/* ->middleware(['auth:sanctum', 'check_admin']); */

    Route::post('admin/markAsRead', [App\Http\Controllers\RequestController::class, 'markAsRead'])->name('request.markAsRead');/* ->middleware(['auth:sanctum', 'check_admin']); */



    Route::get('/admin/messages/center', [App\Http\Controllers\MessageController::class, 'adminMessagesCenter'])->name('messages.admin');/* ->middleware(['auth:sanctum', 'check_admin']); */

    Route::get('/admin/messages/center/{userId}', [App\Http\Controllers\MessageController::class, 'showConversation']);/* ->middleware(['auth:sanctum', 'check_admin']); */

    Route::post('/admin/messages/center/{userId}', [App\Http\Controllers\MessageController::class, 'sendReply'])->name('messages.reply');/* ->middleware(['auth:sanctum', 'check_admin']); */

    Route::delete('/messages/center/delete/{msgID}', [App\Http\Controllers\MessageController::class, 'deleteMessage'])->name('messages.admin.delete');/* ->middleware(['auth:sanctum', 'check_admin']); */

    Route::post('admin/messages/center/sendVoice/{id}', [App\Http\Controllers\MessageController::class, 'sendVoiceByAdmin'])->name('admin.messages.center.send.voice');/* ->middleware(['auth:sanctum', 'check_admin']); */

    Route::post('admin/messages/center/sendPhoto/{id}', [App\Http\Controllers\MessageController::class, 'sendPhotoByAdmin'])->name('admin.messages.center.send.Photo');/* ->middleware(['auth:sanctum', 'check_admin']); */
});




Route::middleware(['auth:sanctum'])->group(function () {

    /* Route::get('/createRequest', [App\Http\Controllers\RequestController::class, 'createRequest'])->name('request.create')->middleware(['auth:sanctum']);
    */

    Route::post('/sendRequest', [App\Http\Controllers\RequestController::class, 'sendRequest'])->name('request.send');/* ->middleware(['auth:sanctum']); */



    Route::get('/messages/center', [App\Http\Controllers\MessageController::class, 'messagesCenter'])->name('messages');/* ->middleware(['auth:sanctum']); */

    Route::post('/messages/center/send', [App\Http\Controllers\MessageController::class, 'sendMessageByuser'])->name('messages.send');/* ->middleware(['auth:sanctum']); */

    Route::delete('/messages/center/delete/{msgID}', [App\Http\Controllers\MessageController::class, 'deleteMessage'])->name('messages.delete');/* ->middleware(['auth:sanctum']); */

    /* Route::get('/messages/center/update/{msgID}', [App\Http\Controllers\MessageController::class, 'editMessage'])->name('messages.update')->middleware(['auth:sanctum']);
    */

    Route::post('/messages/center/update/send/{msgID}', [App\Http\Controllers\MessageController::class, 'editMessageSend'])->name('messages.update.send');/* ->middleware(['auth:sanctum']); */

    Route::post('/messages/center/sendPhoto/', [App\Http\Controllers\MessageController::class, 'sendPhotoByUser'])->name('messages.center.send.Photo');/* ->middleware(['auth:sanctum']); */

    Route::post('/messages/center/sendVoice/', [App\Http\Controllers\MessageController::class, 'sendVoiceByUser'])->name('messages.center.send.voice');/* ->middleware(['auth:sanctum']); */
});
