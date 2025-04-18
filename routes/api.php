<?php

use App\Http\Controllers\API\Auth\AuthApiController;
use App\Http\Controllers\API\Customer\CustomerApiController;
use App\Http\Controllers\API\Messages\CustomerMsgController;
use App\Http\Controllers\API\Customer\EinvoiceApiController;
use App\Http\Controllers\API\Folder\FolderApiController;
use App\Http\Controllers\API\Software\CustomerDataApiController;
use App\Http\Controllers\API\Software\FileApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// API RELATED TO SOFTWARE
Route::post('/upload-customer-stock', [FileApiController::class, 'uploadStockBySoft']);
Route::get('/download-customer-stock',[FileApiController::class, 'downloadStockBySoft']);

Route::prefix('medwincustomer')->group(function() {
    Route::post('getbackup', [CustomerDataApiController::class, 'getBackup']);
    Route::post('submit', [CustomerDataApiController::class, 'submit']);    
    Route::post('getdate', [CustomerDataApiController::class, 'getDate']);
    Route::post('register', [CustomerDataApiController::class, 'register']);
    Route::post('einvoice-status', [CustomerDataApiController::class, 'getEinvoiceStatus']);
    Route::post('einvoice-update', [CustomerDataApiController::class, 'updateEinvoice']);
    Route::post('service-update', [CustomerDataApiController::class, 'serviceUpdate']);
    Route::post('delete-customer', [CustomerDataApiController::class, 'deleteCustomer']);
});
// END

Route::group(['prefix' => 'auth'], function() {
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::post('/checkusertoken', [AuthApiController::class, 'checkUserToken']);
});

Route::group(['prefix' => 'auth', 'middleware' => ['auth:sanctum']], function() {
    Route::post('/logout', [AuthApiController::class, 'logout']);
});

Route::group(['prefix' => 'folders', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [FolderApiController::class, 'index'])->middleware('permission:folder_view');
    Route::post('/', [FolderApiController::class ,'store']);
    Route::put('/{id}', [FolderApiController::class, 'edit']);
    Route::delete('/{id}', [FolderApiController::class, 'deleteFolder']);
    Route::post('/upload-files', [FolderApiController::class, 'uploadFiles']);
    Route::get('/download-file/{id}', [FolderApiController::class, 'downloadFile']);
    Route::get('/folder-info/{id}', [FolderApiController::class, 'getFolderInfo']);
    Route::get('/folder-permission-users/{id}', [FolderApiController::class, 'getUserFolderPermissions']);
    Route::put('/folder-permission-users/{id}', [FolderApiController::class, 'updateUserFolderPermissions']);
});

Route::group(['prefix' => 'customers', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [CustomerApiController::class, 'get'])->middleware('permission:customer_master_view');
    Route::put('/update-action/{acctno}', [CustomerApiController::class, 'updateAction']);
    Route::get('/e-invoices', [EinvoiceApiController::class, 'get'])->middleware('permission:customer_einvoice_view');
    Route::delete('/e-invoices/{id}', [EinvoiceApiController::class, 'delete']);
    Route::get('/stock-access', [CustomerApiController::class, 'getStockAccess'])->middleware('permission:customer_stockaccess_view');
    Route::get('/backup-access', [CustomerApiController::class, 'getBackupAccess'])->middleware('permission:customer_backup_view');
    Route::get('/customer-registers', [CustomerApiController::class, 'getCustomerRegisters'])->middleware('permission:customer_register_view');
    Route::delete('/customer-registers/bulk-delete', [CustomerApiController::class, 'bulkDeleteCustomerRegisters'])->middleware('permission:customer_register_delete');
    Route::get('/customer-whatsapps', [CustomerApiController::class, 'getCustomerWhatsapps'])->middleware('permission:customer_whatsapp_view');
    Route::get('/customer-dashboard-details', [CustomerApiController::class, 'getCustomerDashboardDetails']);
    Route::get('/customer-amc-due', [CustomerApiController::class, 'getCustomerAmcDue'])->middleware('permission:customer_amcdue_view');
    Route::put('/update-password/{acctno}', [CustomerApiController::class, 'updatePassword'])->middleware('permission:customer_backup_toggle');
    Route::get('/messages', [CustomerMsgController::class, 'get'])->middleware('permission:messages_view');
    Route::put('/messages', [CustomerMsgController::class, 'update'])->middleware('permission:messages_update');
    Route::delete('/messages', [CustomerMsgController::class, 'delete'])->middleware('permission:messages_update');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
