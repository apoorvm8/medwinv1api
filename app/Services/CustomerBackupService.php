<?php

namespace App\Services;

use App\Models\CustomerBackup;
use App\Models\CustomerStockAccess;
use App\Models\Einvoice;
use App\Models\FolderMaster;
use App\Models\User;
use App\Traits\HashIds;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerBackupService
{
   use HashIds;

   public function getCustomerBackupAccess($params) {
      
      $query = CustomerBackup::
      select('customer_data.subdesc', 'customer_data.gstno', 'customer_backups.*')->join('customer_data', 'customer_data.acctno', '=', 'customer_backups.acctno');

      if(isset($params["quickFilter"]) && $params["quickFilter"]) {
         $keyword = $params["quickFilter"];
         // $query->where('subdesc',$params["quickFilter"]);

         $query->where(function($sql) use($keyword) {
            foreach(CustomerBackup::SEARCHABLE as $field) {
   
               if($field == "customer_backups.install_date") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(customer_backups.install_date,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
                  continue;
               }
   
               if($field == "customer_backups.next_amc_date") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(customer_backups.next_amc_date,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
                  continue;
               }
   
               if($field == "customer_backups.created_at") {
                  $sql->orWhere(DB::raw("DATE_FORMAT(customer_backups.created_at,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
                  continue;
               }
   
               $sql->orWhere($field, 'LIKE', '%'.$keyword.'%');
            }
         });
      }

      if(isset($params["status"]) && $params["status"]) {
         $status = json_decode($params["status"], true);
         if($status["value"] != -1) {
            $query->where('active', $status["value"]);
         }
      }

      if(isset($params['sortOrder']) && $params['sortOrder']) {
         $sortOrder = json_decode($params['sortOrder'], true);
         $query->orderBy($sortOrder['field'], $sortOrder['sort']);
      }

      return $query->paginate($params["pageSize"], ['*'], 'page', $params['page']);
   }

   public function deleteBackupAccess($id) {
      $id = $this->decode($id, "Customer Backup");
      DB::transaction(function() use($id){
         // Delete the folder first
         $customerBackup = CustomerBackup::find($id);
         $folderId = $customerBackup->folder_id;
         app(FolderService::class)->deleteFolder($this->encode(['id' => $folderId]), true);
         $customerBackup->delete();
      });
   }

   public function getBackupByType($type = "currentyear", $customerFolder) : array {
      $fileArr = [];
      if($type == "currentyear") {
          $folder = FolderMaster::where('parent_id', $customerFolder->id)->where('name', 'currentyear')->where('depth', 3)->where('resource_type', FolderMaster::RESOURCE_TYPE_FOLDER)->first(); 
          $fileArr = [];
          if(!$folder) {
              $fileArr = [];
          } else {
              $fileLst = FolderMaster::where('parent_id', $folder->id)->where('depth', 4)->where('resource_type', FolderMaster::RESOURCE_TYPE_FILE)->orderby('created_at', 'asc')->get();
              $fileArr = [];
              foreach($fileLst as $key => $file) {
                  $arr = [];
                  $arr["id"] = Crypt::encrypt($file->id);
                  $arr["name"] = $file->name;
                  $arr["created_at"] = Carbon::parse($file->created_at)->format('d/m/Y, g:i A');
                  $arr["lastUploadedAt"] = isset($file->updated_at) ? Carbon::parse($file->updated_at)->format('d/m/Y, g:i A') : null;
                  // Get the path size from s3
                  $fileSizeBytes = Storage::disk('s3')->size($file->path);
                  $arr["fileSize"] = humanFileSize($fileSizeBytes);
                  $arr["path"] = $file->path;
                  $fileArr[] = $arr;
              }
          }        
      } else if($type == "lastyear") {
          $folder = FolderMaster::where('parent_id', $customerFolder->id)->where('name', 'lastyear')->where('depth', 3)->where('resource_type', FolderMaster::RESOURCE_TYPE_FOLDER)->first(); 
          $fileArr = [];
          if(!$folder) {
              $fileArr = [];
          } else {
              $fileLst = FolderMaster::where('parent_id', $folder->id)->where('depth', 4)->where('resource_type', FolderMaster::RESOURCE_TYPE_FILE)->orderby('created_at', 'asc')->get();
              $fileArr = [];
              foreach($fileLst as $key => $file) {
                  $arr = [];
                  $arr["id"] = Crypt::encrypt($file->id);
                  $arr["name"] = $file->name;
                  $arr["created_at"] = Carbon::parse($file->created_at)->format('d/m/Y, g:i A');
                  $arr["lastUploadedAt"] = isset($file->updated_at) ? Carbon::parse($file->updated_at)->format('d/m/Y, g:i A') : null;
                  $fileSizeBytes = Storage::disk('s3')->size($file->path);
                  $arr["fileSize"] = humanFileSize($fileSizeBytes);
                  $arr["path"] = $file->path;
                  $fileArr[] = $arr;
              }
          }        
      } else if($type == "other") {
         $folder = FolderMaster::where('parent_id', $customerFolder->id)->where('name', 'other')->where('depth', 3)->where('resource_type', FolderMaster::RESOURCE_TYPE_FOLDER)->first(); 
         $fileArr = [];
         if(!$folder) {
             $fileArr = [];
         } else {
             $fileLst = FolderMaster::where('parent_id', $folder->id)->where('depth', 4)->where('resource_type', FolderMaster::RESOURCE_TYPE_FILE)->orderby('created_at', 'asc')->get();
             $fileArr = [];
             foreach($fileLst as $key => $file) {
                 $arr = [];
                 $arr["id"] = Crypt::encrypt($file->id);
                 $arr["name"] = $file->name;
                 $arr["created_at"] = Carbon::parse($file->created_at)->format('d/m/Y, g:i A');
                 $arr["lastUploadedAt"] = isset($file->updated_at) ? Carbon::parse($file->updated_at)->format('d/m/Y, g:i A') : null;
                 $fileSizeBytes = Storage::disk('s3')->size($file->path);
                 $arr["fileSize"] = humanFileSize($fileSizeBytes);
                 $arr["path"] = $file->path;
                 $fileArr[] = $arr;
             }
         }     
      }
      return $fileArr;
  }

  /**
   * @desc Function to create other folder inside a customer backup's folder structure
   * @access-control private
   * @params $sourceId, $user
   */
  public function createOtherFolderForCustomer($sourceId, $adminUser) {
      $sourceId = $this->decode($sourceId, 'Customer Backup');
      $customerBackup = CustomerBackup::find($sourceId);
      $user = User::where('email', config('app.mainuseremail'))->first();
      if($customerBackup) {
         $permissionRows = null;
         if($user) {
            $permissionRows = [
               [
                  'view' => 1,
                  'create' => 0,
                  'edit' => 0,
                  'info' => 1,
                  'download' => 1,
                  'upload' => 0,
                  'user_id' => $this->encode(['id' => $user->id]),
                  'folder_id' => null,
                  'delete' => 0,
                  'permission' => false,
                  'applychild' => false
               ]
            ];
         }

         app(FolderService::class)->createFolder(['parent_id' => $this->encode(['id' => $customerBackup->folder_id]), 'name' => 'other', 
         'resource_module' => 'backup', 'permissionRows' => $permissionRows], $adminUser ? $adminUser->id : null);
      } else {
         throw new Exception("Customer Backup not found.");
      }
  }
}

