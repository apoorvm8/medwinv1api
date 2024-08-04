<?php

use App\Http\Controllers\API\Software\FileApiController;
use App\Http\Controllers\Web\Auth\CustomerLoginController;
use App\Http\Controllers\Web\CustomerMsgController;
use App\Http\Controllers\Web\CustomersController;
use App\Http\Controllers\Web\PagesController;
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

Route::get("sitemap.xml" , function () {
    return \Illuminate\Support\Facades\Redirect::to('sitemap.xml');
});

Route::post('/backup-file-bysoft', [FileApiController::class, 'backupFileBySoft']);
Route::get('/downloadBySoft', [FileApiController::class, 'downloadBySoft']);
Route::get('/downloadBySoftOther', [FileApiController::class, 'downloadBySoftOther']);
Route::post('/uploadBySoft', [FileApiController::class, 'uploadBySoft']);

// Redirect to frontend
Route::get('/admin', function() {
    return redirect(config('app.frontend_url'));
});

Route::group(['prefix' => 'customer'], function() {
    Route::post('/login', [CustomerLoginController::class, 'login'])->name('customer.login');
    Route::get('/', [CustomersController::class, 'dashboard'])->name('customer.dashboard');
    Route::post('/logout', [CustomerLoginController::class, 'logout'])->name('customer.logout');
    Route::get('/backup', [CustomersController::class, 'backup'])->name('customer.backup');
    Route::get('/fetch-backup', [CustomersController::class, 'fetchBackup'])->name('customer.fetchbackup');
    Route::get('/file-download/{fileId}', [CustomersController::class, 'fileDownload']);
    Route::get('/change-password', [CustomersController::class, 'changePassword'])->name('customer.changepassword');
    Route::put('/update-password', [CustomersController::class, 'updatePassword'])->name('customer.updatepassword');
});

Route::get('/', [PagesController::class, 'index'])->name('index');
Route::get('/contact', [PagesController::class, 'contact'])->name('contact');
Route::get('/terms_and_conditions', [PagesController::class, 'terms_and_condition'])->name('terms_and_conditions');
Route::get('/sitemap', [PagesController::class, 'sitemap'])->name('sitemap');
Route::get('retail/pharma', [PagesController::class, 'retail_pharma'])->name('pharmacy');
Route::get('retail/bookstore', [PagesController::class, 'retail_bookstore'])->name('bookstore');
Route::get('retail/footwear', [PagesController::class, 'retail_footwear'])->name('footwear');
Route::get('retail/departmental', [PagesController::class, 'retail_departmental'])->name('departmental');
Route::get('/downloads', [PagesController::class, 'downloads'])->name('downloads');
Route::get('/file_download/{fileId}', [PagesController::class, 'file_download'])->name('pages.download');
//Route::get('/file_download_all', 'PagesController@file_download_all')->name('pages.download.all');
Route::get('getFiles', [PagesController::class, 'getFiles'])->name('pages.getFiles');
// Function to download file by software
Route::get('/fileDownloadByName/{fileName}', [PagesController::class, 'fileDownloadByName']);
Route::post('/contact', [CustomerMsgController::class,'store'])->name('customer.msg.submit');
