<?php

use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm']);
Auth::routes();
Route::get('/foo', function () {
    Artisan::call('storage:link');
    dd('Link Created Successfully.');
});
Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    // return "Cache is cleared";
    return view('cache');
})->name('cache.clear');

Route::middleware('auth')->group(function () {
	Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
	Route::resource('users', App\Http\Controllers\UserController::class);
	Route::resource('auctions', App\Http\Controllers\AuctionController::class);	
	Route::resource('bids', App\Http\Controllers\BidController::class);
	Route::post('/saveInvites', [App\Http\Controllers\AuctionController::class, 'saveAuctionParticipants'])->name('auction.saveInvites');	

	Route::get('/joinauction/{token}', [App\Http\Controllers\AuctionController::class, 'sellerJoinAuction'])->name('joinauction');
	Route::post('/acceptinvite', [App\Http\Controllers\AuctionController::class, 'acceptInvite'])
	 	->name('acceptinvite');
	// Route::get('/joinauction/{token}', [App\Http\Controllers\AuctionController::class, 'acceptInvite'])
	// 	->name('joinauction');	
	// Route::get('/viewauction/{id}', [App\Http\Controllers\AuctionController::class, 'sellerView'])
	// 	->name('viewauction');	
	Route::get('/sellerauction/{id}', [App\Http\Controllers\BidController::class, 'viewSellerAuction'])
		->name('sellerauction');

	// Route::get('/sellerauctions', [App\Http\Controllers\BidController::class, 'sellerAuctionBids'])
	//  	->name('sellerauctions');

	Route::post('/updatebid', [App\Http\Controllers\BidController::class, 'updateLastBid'])->name('bids.updatebid');
	
	Route::post('/extendtime', [App\Http\Controllers\AuctionController::class, 'updateAuctionTime'])->name('auctions.extendtime');

	// Route::get('/liveauction/{id}', [App\Http\Controllers\BidController::class, 'buyerLiveAuction'])
	// 	->name('liveauction');		

	// Route::get('/choosewinner/{id}', [App\Http\Controllers\BidController::class, 'chooseWinner'])
	// 	->name('choosewinner');
	
	Route::post('/savewinner', [App\Http\Controllers\BidController::class, 'saveWinner'])->name('savewinner');
});

Route::get('/t', function () {
    event(new \App\Events\NewBid());
    dd('Event Run Successfully.');
});