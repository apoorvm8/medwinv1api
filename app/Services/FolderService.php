<?php

namespace App\Services;

use App\Models\CustomerBackup;
use App\Models\CustomerStockAccess;
use App\Models\FolderMaster;
use App\Models\FolderPermission;
use App\Models\User;
use App\Traits\HashIds;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use JsonException;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;
use Illuminate\Support\Str;

class FolderService
{
   use HashIds;

   // The signed-in user's folder access, loaded once per request by access().
   private $access = null;

   /**
    * @desc Function to fetch folders based on filter
    * @return array
    */
   public function getAll($filters) {

      try {
         $filters = json_decode($filters, true, 512, JSON_THROW_ON_ERROR);
         if(isset($filters["parent_id"]) && $filters["parent_id"] != null) {
            $filters["parent_id"] = $this->decode($filters["parent_id"]);
         } 

         return FolderMaster::getAll($filters);
      } catch (JsonException $ex) {
         throw new JsonException("Error in parsing JSON", $ex->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
      }
   }

   /**
    * @desc Function to list a folder for the folder browser. Without a search or filters this is the
    * folder's own contents; with them it is everything below the folder that matches. Sizes and
    * modification times come from one S3 listing (folders have neither, as in the S3 console).
    * Only items the user may view are listed, and each one says what the user may do with it.
    * @param array params folder, search, type, modified, from, to, area, sort, dir, page, per_page
    * @return array
    */
   public function browse($params) {
      $folder = $this->authorize(Arr::get($params, 'folder'), 'view');
      if($folder->resource_type != FolderMaster::RESOURCE_TYPE_FOLDER) {
         abort(Response::HTTP_NOT_FOUND, "Folder not found.");
      }

      $search = trim(Arr::get($params, 'search') ?? '');
      $type = Arr::get($params, 'type') ?: 'all';
      $modified = Arr::get($params, 'modified') ?: 'any';
      $area = Arr::get($params, 'area') ?: 'all';
      $finding = mb_strlen($search) >= 2 || $type != 'all' || $modified != 'any' || $area != 'all';

      if($finding) {
         $query = FolderMaster::where('path', 'like', $this->escapeLike($folder->path) . '/%');
         if(mb_strlen($search) >= 2) {
            $query->where('name', 'like', '%' . $this->escapeLike($search) . '%');
         }
         if($type != 'all') {
            $query->where('resource_type', $type);
         }
         if($area != 'all') {
            $query->where('resource_module', $area);
         }
      } else {
         $query = FolderMaster::where('parent_id', $folder->id);
      }
      $rows = $query->get()->filter(fn ($row) => $this->allows($row, 'view'));

      // Sizes and modification times of the files, from one listing of the folder (deep when finding).
      $objects = [];
      if($rows->contains('resource_type', FolderMaster::RESOURCE_TYPE_FILE)) {
         foreach(Storage::disk('s3')->getDriver()->listContents($folder->path, $finding) as $object) {
            if($object->isFile()) {
               $objects[$object->path()] = $object;
            }
         }
      }
      $items = $rows->map(fn ($row) => $this->browserItem($row, $objects[$row->path] ?? null));

      // Only files have a modification time, so a date filter leaves folders out.
      if($modified != 'any') {
         $from = [
            'today' => now()->startOfDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            'custom' => Arr::get($params, 'from') ? Carbon::parse(Arr::get($params, 'from'))->startOfDay() : null,
         ][$modified];
         $to = $modified == 'custom' && Arr::get($params, 'to') ? Carbon::parse(Arr::get($params, 'to'))->endOfDay() : null;
         $items = $items->filter(fn ($item) => $item['last_modified'] !== null
            && (!$from || $item['last_modified'] >= $from->timestamp)
            && (!$to || $item['last_modified'] <= $to->timestamp));
      }

      // Folders first, then by the chosen column; names break ties.
      $sort = Arr::get($params, 'sort') ?: 'name';
      $descending = Arr::get($params, 'dir') == 'desc';
      $items = $items->sort(function ($a, $b) use ($sort, $descending) {
         if($a['resource_type'] != $b['resource_type']) {
            return $a['resource_type'] == FolderMaster::RESOURCE_TYPE_FOLDER ? -1 : 1;
         }
         $order = $sort == 'name' ? strnatcasecmp($a['name'], $b['name']) : ($a[$sort] ?? -1) <=> ($b[$sort] ?? -1);
         return ($descending ? -$order : $order) ?: strnatcasecmp($a['name'], $b['name']);
      });

      // The folders from root down to this one: root/a/b gives root, root/a and root/a/b.
      $segments = explode('/', $folder->path);
      $paths = array_map(fn ($length) => implode('/', array_slice($segments, 0, $length)), range(1, count($segments)));
      $breadcrumbs = FolderMaster::whereIn('path', $paths)->orderBy('depth')->get()
         ->map(fn ($crumb) => ['id' => $this->encode($crumb), 'name' => $crumb->name]);

      return [
         'folder' => $this->browserItem($folder),
         'breadcrumbs' => $breadcrumbs,
         'finding' => $finding,
         'total' => $items->count(),
         'items' => $items->forPage(intval(Arr::get($params, 'page') ?: 1), intval(Arr::get($params, 'per_page') ?: 25))->values(),
      ];
   }

   /**
    * @desc Function to shape a folder or file for the folder browser
    * @param FolderMaster row
    * @param \League\Flysystem\FileAttributes object its S3 listing entry, if any
    * @return array
    */
   private function browserItem(FolderMaster $row, $object = null) {
      return [
         'id' => $this->encode($row),
         'parent_id' => $row->parent_id ? $this->encode($row, 'parent_id') : null,
         'name' => $row->name,
         'slug' => $row->slug,
         'depth' => $row->depth,
         'path' => $row->path,
         'location' => Str::beforeLast($row->path, '/'),
         'resource_type' => $row->resource_type,
         'resource_module' => $row->resource_module,
         'size' => $object ? $object->fileSize() : null,
         'last_modified' => $object ? $object->lastModified() : null,
         'downloads' => intval($row->times_downloaded),
         'can' => array_combine(FolderMaster::PERMISSION_KEYS, array_map(fn ($action) => $this->allows($row, $action), FolderMaster::PERMISSION_KEYS)),
      ];
   }

   private function escapeLike($value) {
      return addcslashes($value, '%_\\');
   }

   /**
    * @desc Function to find a folder or file by its hashed id (no id means the root folder, as in
    * createFolder) and stop the request when the signed-in user may not do the action on it
    * @param string id
    * @param string action one of FolderMaster::PERMISSION_KEYS
    * @return FolderMaster
    */
   public function authorize($id, $action) {
      $folderId = null;
      if($id) {
         try {
            $folderId = $this->decode($id, "Folder");
         } catch(Exception $ex) {
            // An id that doesn't decode (a broken link) is as missing as one that isn't in the table.
            abort(Response::HTTP_NOT_FOUND, "Folder or file not found.");
         }
      }
      $folder = $id ? FolderMaster::find($folderId) : FolderMaster::where('slug', 'root')->first();
      if(!$folder) {
         abort(Response::HTTP_NOT_FOUND, "Folder or file not found.");
      }
      // No name in the message: ids are guessable, so it would tell folder names to users who can't see them.
      if(!$this->allows($folder, $action)) {
         abort(Response::HTTP_FORBIDDEN, "You do not have $action permission on this folder or file.");
      }
      return $folder;
   }

   /**
    * @desc Function to tell whether the signed-in user may do an action on a folder or file, by the
    * same rules the folder screen has always followed:
    * - everyone may open the root folder; superusers may also create, upload, download and see info there;
    * - superusers may do anything anywhere else;
    * - other users need the action in their folder_permissions row, and must be able to view the item
    *   and every folder above it (the only way the folder tree ever let them reach it);
    * - creating, editing and uploading only apply to folders.
    * @param FolderMaster folder
    * @param string action one of FolderMaster::PERMISSION_KEYS
    * @return bool
    */
   public function allows(FolderMaster $folder, $action) {
      if(in_array($action, ['create', 'edit', 'upload']) && $folder->resource_type != FolderMaster::RESOURCE_TYPE_FOLDER) {
         return false;
      }

      $access = $this->access();
      if($folder->slug == 'root') {
         return $action == 'view' || ($access['superuser'] && in_array($action, ['create', 'upload', 'download', 'info']));
      }
      if($access['superuser']) {
         return true;
      }

      $permission = $access['permissions'][$folder->id] ?? null;
      if(!$permission || !$permission[$action]) {
         return false;
      }

      // root/a/b needs root/a and root/a/b to be viewable.
      $segments = explode('/', $folder->path);
      for($length = 2; $length <= count($segments); $length++) {
         if(!isset($access['viewablePaths'][implode('/', array_slice($segments, 0, $length))])) {
            return false;
         }
      }
      return true;
   }

   /**
    * @desc Function to load the signed-in user's folder access once per request: whether they are a
    * superuser, their folder_permissions rows by folder id, and the paths of the folders they may view
    * @return array
    */
   private function access() {
      if($this->access === null) {
         $user = auth('sanctum')->user();
         $superuser = $user->hasRole(User::SUPER_USER);
         $permissions = $superuser ? collect() : FolderPermission::where('user_id', $user->id)->get()->keyBy('folder_id');

         $this->access = [
            'superuser' => $superuser,
            'permissions' => $permissions,
            'viewablePaths' => FolderMaster::whereIn('id', $permissions->filter(fn ($permission) => $permission->view)->keys())
               ->pluck('path')->flip()->all(),
         ];
      }
      return $this->access;
   }

   /**
   * @desc Function to create a folder
   * @param array data
   * @return void
   */
   public function createFolder($data, $userId = null) {
      $permissionRows = null;
      $actionType = null;
      $acctNo = null;

      if(Arr::exists($data, 'permissionRows')) {
         $permissionRows = Arr::get($data, 'permissionRows');
         $data = Arr::except($data, 'permissionRows');
      }

      $parentId = Arr::get($data, 'parent_id');
      if($parentId == null) {
         $root = FolderMaster::where('slug', 'root')->first();
         if(!$root) {
            throw new ModelNotFoundException("Folder not found", Response::HTTP_NOT_FOUND);
         }

         $parentId = $this->encode($root->id);
      }

      $parentId = $this->decode($parentId);
      $parentFolder = FolderMaster::find($parentId);
      if(!$parentFolder) {
         throw new ModelNotFoundException("Folder not found", Response::HTTP_NOT_FOUND);
      }
      $parentPath = $parentFolder->path;
      $parentName = $parentFolder->name;
      if(!$parentPath) {
         $parentPath = 'root';
      }

      $folder = FolderMaster::where('parent_id', $parentId)->where('name', Arr::get($data, 'name'))->first();
      if($folder) {
         throw new Exception("Folder with name " . Arr::get($data, 'name') . " already created inside $parentName", Response::HTTP_BAD_REQUEST);
      }

      try {
         // Create the folder
         // Check if there is a fromSource key
         if(isset($data['fromSource']) && $data['fromSource'] && $data['fromSource'] != null) {
            if($data['fromSource']['sourceName'] && $data['fromSource']['sourceName'] == 'customermaster') {
               if($data['fromSource']['data']['acctNo']) {
                  $acctNo = $data['fromSource']['data']['acctNo'];
                  $folderName = Arr::get($data, 'name');
                  // Replace any whitespace with underscore
                  $folderName = preg_replace('/\s+/', '_', $folderName);
                  // Now append the special notiation for backup
                  $keyword = "_backup_";
                  $actionType = "backup";
                  if($data['fromSource']['data']['actionType'] && $data['fromSource']['data']['actionType'] == 'adminFolderActionStock') {
                     $keyword = "_stock_";
                     $actionType = "stock";
                  }
                  $folderName = "c$acctNo" . "$keyword" . $folderName;
                  
                  Arr::set($data, 'name', $folderName); 
                  // Again check with the above modified folder name
                  $folder = FolderMaster::where('parent_id', $parentId)->where('name', Arr::get($data, 'name'))->first();
                  if($folder) {
                     throw new Exception("Folder with name " . Arr::get($data, 'name') . " already created inside $parentName", Response::HTTP_BAD_REQUEST);
                  }
               }
            }
         }

         DB::transaction(function () use($data, $parentFolder, $parentPath, $permissionRows, $actionType, $acctNo, $userId){
            // Make the S3 folder
            $created = Storage::disk('s3')->makeDirectory($parentPath . '/' . Arr::get($data, 'name'));
            if(!$created) {
               throw new Exception("Error in creating s3 object");
            }

            $slug = SlugService::createSlug(FolderMaster::class, 'slug', Arr::get($data, 'name'), ['unique' => true]);
            Arr::set($data, 'depth', $parentFolder->depth + 1);
            Arr::set($data, 'slug', $slug);
            Arr::set($data, 'children_count', json_encode(['folders' => 0, 'files' => 0]));
            Arr::set($data, 'resource_type', FolderMaster::RESOURCE_TYPE_FOLDER);
            Arr::set($data, 'created_at', now());
            Arr::set($data, 'created_by', $userId);
            Arr::set($data, 'path', $parentPath . '/' . Arr::get($data, 'name'));
            Arr::set($data, 'parent_id', $parentFolder->id);
            Arr::set($data, 'resource_module', Arr::get($data, 'resource_module') ? Arr::get($data, 'resource_module') : 'software');
            $createdFolder = FolderMaster::create($data);
            // $this->resetChildCountUp($parentFolder);
            if($permissionRows) {
               $this->updateUserFolderPermissions($permissionRows, $this->encode(['id' => $createdFolder->id]), true, $userId);
            }

            // If action type is present, then based on value create the entry in respective child table
            if($actionType) {
               if($actionType == "backup") {
                  // Create entry in backup table
                  // Recursive Create the child folders -> currentyear and lastyear
                  $this->createFolder(['parent_id' => $this->encode(['id' => $createdFolder->id]), 'name' => 'currentyear', 
                  'resource_module' => 'backup', 'permissionRows' => $permissionRows], $userId);
                  $this->createFolder(['parent_id' => $this->encode(['id' => $createdFolder->id]), 'name' => 'lastyear',
                  'resource_module' => 'backup', 'permissionRows' => $permissionRows], $userId);
                  // $this->resetChildCountUp($parentFolder);

                  CustomerBackup::create([
                     'acctno' => $acctNo, 'folder_id' => $createdFolder->id, 'active' => 1, 'created_at' => now(),
                     'number_of_backup' => json_encode(['currentyear' => 3, 'lastyear' => 3])
                  ]);
               }

               if($actionType == "stock") {
                  CustomerStockAccess::create([
                     'acctno' => $acctNo, 'folder_id' => $createdFolder->id, 'active' => 1, 'created_at' => now()
                  ]);
               }
            }
         });
      } catch(Exception $ex) {
         throw new Exception($ex->getMessage() ?: "Error in creating folder", $ex->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
      }
   }

   public function editFolder($data) {
      $folderId = $this->decode($data['id'], "Folder");
      $folder = FolderMaster::find($folderId);
      if(!$folder)  
         throw new Exception("Folder not found");

      $parentFolder = FolderMaster::find($folder->parent_id);
      if(!$parentFolder) 
         throw new Exception("Parent folder not found");
      
      $duplicateCheck = FolderMaster::where('parent_id', $folder->parent_id)->where('name', Arr::get($data, 'name'))->first();
      if($duplicateCheck) {
         if($folder->resource_module == $data['resource_module']) {
            throw new Exception("Folder with name " . Arr::get($data, 'name') . " already created inside $parentFolder->name", Response::HTTP_BAD_REQUEST);
         } else {
            // Just update the resource module
            if($folder->depth < 2) {
               $this->updateDescendantsModule($folder, $data['resource_module']);
               $folder->resource_module = $data['resource_module']; 
               $folder->save();
               return $folder;
            } else {
               throw new Exception("Folder with name " . Arr::get($data, 'name') . " resource module cannot be changed.", Response::HTTP_BAD_REQUEST);
            }
         }
      }

      $currentPath = $folder->path;
      $currentFolderName = $folder->name;
      $newFolderName = $data['name'];
      $newPath = (!$parentFolder->path ? "root" : $parentFolder->path) . "/" . $data["name"];
      $updatedModuleName = $folder->depth < 2 ? $data['resource_module'] : $folder->resource_module;

      try {
         $folder = DB::transaction(function() use($folder, $currentPath, $newPath, $newFolderName, $updatedModuleName) {
            // First update the folder path of all children
            $this->updateDescendantsPath($folder, $newPath, $updatedModuleName);

            // Update the folder name and path 
            $slug = SlugService::createSlug(FolderMaster::class, 'slug', $newFolderName, ['unique' => true]);
            $folder->name = $newFolderName;
            $folder->path = $newPath;
            $folder->slug = $slug;
            $folder->resource_module = $updatedModuleName;
            $folder->save();

            // S3 update
            $files = Storage::disk('s3')->allFiles($currentPath);
            $dirs = Storage::disk('s3')->allDirectories($currentPath);
            if(count($dirs) > 0) {
               $this->moveEmptyDirectories($dirs, $folder->depth, $newFolderName);
            }

            if(count($files) > 0) {
               foreach($files as $file) {
                  $segments = explode("/", $file);
                  $segments[$folder->depth] = $newFolderName;
                  $newFilePath = implode('/', $segments);
                  Storage::disk('s3')->copy($file, $newFilePath);
               }
            }

            // If both files and directories are empty that means we are changing and empty folders name so directly make directory
            if(count($dirs) == 0 && count($files) == 0) {
               Storage::disk('s3')->makeDirectory($newPath);
            }

            Storage::disk('s3')->deleteDirectory($currentPath);

            return $folder;
         });
         return $folder;
      } catch(Exception $ex) {
         throw new Exception ("Error in updating folder information.");
      }
   }

   public function moveEmptyDirectories($dirs, $depth, $newFolderName) {
      foreach($dirs as $dir) {
         $doesHaveFiles = Storage::disk('s3')->allFiles($dir);
         if(count($doesHaveFiles) == 0) {
            $doesHaveDirectory = Storage::disk('s3')->allDirectories($dir);
            if(count($doesHaveDirectory) > 0) {
               $this->moveEmptyDirectories($doesHaveDirectory, $depth, $newFolderName);
            } else {
               $segments = explode("/", $dir);
               $segments[$depth] = $newFolderName;
               $newFilePath = implode('/', $segments);
               Storage::disk('s3')->makeDirectory($newFilePath);
            }
         }
      }
   }

   /**
    * @desc Recusrsively update the children's path
    * @param FolderMaster $folder
    * @param string $updatedModuleName
    * @return void
    */
   public function updateDescendantsModule(FolderMaster $folder, string $updatedModuleName) {
      $children = FolderMaster::where('parent_id', $folder->id)->get();
      if($children && count($children) > 0) {
         foreach($children as $child) {
            $child->resource_module = $updatedModuleName;
            $child->save();
            $this->updateDescendantsModule($child, $updatedModuleName);
         }
      } 
   }

   /**
    * @desc Recusrsively update the children's path
    * @param FolderMaster $folder
    * @param string $newPath
    * @param string $updatedModuleName
    * @return void
    */
   public function updateDescendantsPath(FolderMaster $folder, string $newPath, string $updatedModuleName) {
      $children = FolderMaster::where('parent_id', $folder->id)->get();
      if($children && count($children) > 0) {
         foreach($children as $child) {
            $child->path = $newPath . "/" . $child->name;
            $child->resource_module = $updatedModuleName;
            $child->save();
            $this->updateDescendantsPath($child, $child->path, $updatedModuleName);
         }
      } 
   }

   /**
    * @desc Function to create the folder permissions
    * @param array permissionRows
    * @param int folderId
    * @return void
    */
   public function updateUserFolderPermissions($permissionRows, $folderId, $toUpdate = false, $createdByUserId = null) {
      // Loop through permissionRows if all flags are false then that row will not be created in table to save space
      $folderId = $this->decode($folderId, "Folder");
     
      foreach($permissionRows as $permissionRow) {
         // Check if permission row has applychild set to true
         $permissionRow['permission'] = false;
         $mainFolder = FolderMaster::find($folderId);
         if($mainFolder->resource_type == FolderMaster::RESOURCE_TYPE_FILE) {
            $permissionRow['upload'] = false;
            $permissionRow['applytochild'] = false;
            $permissionRow['create'] = false;
            $permissionRow['edit'] = false;
         }

         if($permissionRow['applychild']) {
            $permissionRow['applychild'] = false; // set to false so it dosen't go in infinite loop.
            // Get the Folder path from 
            $path = $mainFolder->path;
            $allFolders = Storage::disk('s3')->allDirectories($path);
            $allFiles = Storage::disk('s3')->allFiles($path);
            // Loop through alll allfodlers
            foreach($allFolders as $folderItem) {
               $childFolder = FolderMaster::where('path', $folderItem)->first();
               if($childFolder) {
                  $this->updateUserFolderPermissions([$permissionRow], $this->encode(['id' => $childFolder->id]), $toUpdate, $createdByUserId);
               }
            }

            foreach($allFiles as $fileItem) {
               $childFile = FolderMaster::where('path', $fileItem)->first();
               if($childFile) {
                  $this->updateUserFolderPermissions([$permissionRow], $this->encode(['id' => $childFile->id]), $toUpdate, $createdByUserId);
               }
            }
         }

         $shouldCreate = false;
         $userId = $this->decode($permissionRow["user_id"]);
         foreach(FolderMaster::PERMISSION_KEYS as $key) {
            if($permissionRow[$key] == true) {
               $shouldCreate = true;
            }  
         }
         if($shouldCreate) {
            if(!$toUpdate) {
               FolderPermission::create([
                  'folder_id' => $folderId,
                  'user_id' => $userId,
                  'view' => $permissionRow["view"],
                  'create' => $permissionRow['create'],
                  'edit' => $permissionRow['edit'],
                  'info' => $permissionRow['info'],
                  'permission' => $permissionRow['permission'],
                  'delete' => $permissionRow['delete'],
                  'upload' => $permissionRow['upload'],
                  'download' => $permissionRow['download'],
                  'created_at' => now(),
                  'created_by' => $createdByUserId
               ]);
            } else {
               $checkPermissionRow = FolderPermission::where('folder_id', $folderId)->where('user_id', $userId)->first();
               if($checkPermissionRow) {
                  $checkPermissionRow->update([
                     'view' => $permissionRow["view"],
                     'create' => $permissionRow['create'],
                     'edit' => $permissionRow['edit'],
                     'info' => $permissionRow['info'],
                     'permission' => $permissionRow['permission'],
                     'delete' => $permissionRow['delete'],
                     'upload' => $permissionRow['upload'],
                     'download' => $permissionRow['download'],
                     'updated_at' => now(),
                     'updated_by' => $createdByUserId
                  ]); 
               } else {
                  FolderPermission::create([
                     'folder_id' => $folderId,
                     'user_id' => $userId,
                     'view' => $permissionRow["view"],
                     'create' => $permissionRow['create'],
                     'edit' => $permissionRow['edit'],
                     'info' => $permissionRow['info'],
                     'permission' => $permissionRow['permission'],
                     'delete' => $permissionRow['delete'],
                     'upload' => $permissionRow['upload'],
                     'download' => $permissionRow['download'],
                     'created_at' => now(),
                     'created_by' => $createdByUserId
                  ]);
               }
            }
         } else {
            if($toUpdate) {
               FolderPermission::where('folder_id', $folderId)->where('user_id', $userId)->delete();
            }
         }
      }
   }

   /**
    * TODO:- ADD LOGIC FOR TEMPORARY DIRECTORY IF DELETION FAILS
    * @desc Function to delete folder
    * @param int id
    * @return void
    */
    public function deleteFolder($id, $shouldDeleteFolder) {
      $id = $this->decode($id);
      $folder = FolderMaster::find($id);
      if(!$folder) 
         throw new ModelNotFoundException("Folder or File not found", Response::HTTP_NOT_FOUND);
         
      $folderPath = $folder->path;
      $type = "Folder";

      if($folder->resource_type === FolderMaster::RESOURCE_TYPE_FOLDER) {
         $type = "Folder";
         try {
            DB::transaction(function () use($folder, $folderPath, $shouldDeleteFolder){
   
               if($shouldDeleteFolder) {
                  $deleted = Storage::disk('s3')->deleteDirectory($folderPath);
                  if(!$deleted) {
                     throw new Exception("Error in deleting s3 directory");
                  }
               } else {
                  $files = Storage::disk('s3')->allFiles($folderPath);
                  $dirs = Storage::disk('s3')->allDirectories($folderPath);
                  Storage::disk('s3')->delete($files);
                  foreach($dirs as $dir) {
                     Storage::disk('s3')->deleteDirectory($dir);
                  }
               }
   
               if($shouldDeleteFolder) {
                  $this->deleteDescendantNodes($folder);
                  // $this->resetChildCountUp(FolderMaster::find($folder->parent_id));
                  // $this->resetFolderSizeUp(FolderMaster::find($folder->parent_id));
               } else {
                  $this->deleteDescendantNodes_WithoutOriginFolder($folder);
                  // $this->resetChildCountUp($folder);
                  // $this->resetFolderSizeUp($folder);
               }
            }); 
         } catch(Exception $ex) {
            throw new Exception($ex->getMessage() ?: "Error in deleting folder", $ex->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
         }
      } else {
         $type = "File";
         try {
            DB::transaction(function () use($folder, $folderPath){
               
               $deleted = Storage::disk('s3')->delete($folderPath);
               if(!$deleted) {
                  throw new Exception("Error in deleting s3 file");
               }
              
               // $this->resetChildCountUp(FolderMaster::find($folder->parent_id));
               // $this->resetFolderSizeUp(FolderMaster::find($folder->parent_id));
               $folder->delete();
            }); 
         } catch(Exception $ex) {
            throw new Exception($ex->getMessage() ?: "Error in deleting file", $ex->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
         }
      }
      return $type;
   }

   /**
    * @desc Function to upload multiple files to s3
    * @return void
    */
   public function uploadFiles($data, $userId = null) {
      $folderFiles = Arr::get($data, 'folderFiles');
      $id = $this->decode(Arr::get($data, 'id'));
      $folder = FolderMaster::find($id);
      $parentPath = $folder->path;
      $resourceModule = $folder->resource_module;
      $depth = $folder->depth;
      if(!$parentPath) {
         $parentPath = 'root';
      }
         
      $size = 0;
      $files = [];
      $toUpdateFiles = [];
      foreach($folderFiles as $folderFile) {
         $fileName = $folderFile->getClientOriginalName();
         $size = $size + number_format(filesize($folderFile)/pow(1024, 2), 1);
         
         // Check if this file exists in s3
         if(!Storage::disk('s3')->exists($parentPath . '/' . $fileName)) {
            // if it dosen't exists then add this in the mass creation array

            $slug = SlugService::createSlug(FolderMaster::class, 'slug', $fileName, ['unique' => true]);
            
            $tempFile["parent_id"] = $folder->id;
            $tempFile["name"] = $fileName;
            $tempFile["slug"] = $slug;
            $tempFile["children_count"] = null;
            $tempFile["depth"] = $depth + 1;
            $tempFile["path"] = $parentPath . '/' . $fileName;
            $tempFile["file_size"] = number_format(filesize($folderFile)/pow(1024, 2), 1);
            $tempFile["resource_type"] = FolderMaster::RESOURCE_TYPE_FILE;
            $tempFile["resource_module"] = $resourceModule;
            $tempFile["created_at"] = now();
            $tempFile["created_by"] = $userId;
            
            $files[] = $tempFile;
         } else {
            // If it does exist then add this in the toUpdateFiles array
            $toUpdateFiles[] = FolderMaster::where('path', $parentPath . '/' . $fileName)->first();
         }
      }

      // 100 MB
      if($size > 100) {
         throw new Exception("Total file size cannot exceed 100 MB", Response::HTTP_BAD_REQUEST);
      }
      
      try {
         DB::transaction(function () use($folder, $files, $folderFiles, $toUpdateFiles, $userId){

            // Upload the files
            foreach($folderFiles as $folderFile) {
               $fileNameWithExt = $folderFile->getClientOriginalName();
               $pathToUpload = $folder->path . '/' . $fileNameWithExt;
               $uploaded = Storage::disk('s3')->put($pathToUpload, fopen($folderFile, 'r+'));
               if(!$uploaded) {
                  throw new Exception("Error in uploading s3 object file");
               }
            }

            // Prepare array for mass insertion
            if(count($files) > 0) {
               foreach($files as $file) {
                  $result = FolderMaster::create($file);
                  $this->createFilePermissionIfOwner($userId, $result->id, $folder->id);
               }
            }


            if(count($toUpdateFiles) > 0) {
               // Update last updated at
               $this->updateLastTimeAndPermission($toUpdateFiles, $userId);
               // $this->updateFileSize($toUpdateFiles, $userId);
            }
            // Add total size in upwards direction
            // $this->resetFolderSizeUp($folder);

            // Add the total file count in upwards manner.
            // $this->resetChildCountUp($folder);
            
         }); 
      } catch(Exception $ex) {
         throw new Exception($ex->getMessage() ?: "Error in upload files", intval($ex->getCode()) ?: Response::HTTP_INTERNAL_SERVER_ERROR);
      }
   }

   public function createFilePermissionIfOwner($userId, $fileId, $parentId) {
      // Logic related to permission
      if($userId) {
         $user = User::find($userId);
         $isSuperUser = $user->hasRole(User::SUPER_USER);

         // Don't want to set all this if superadmin is using this
         if(!$isSuperUser) {
            $folderPermission = FolderPermission::where('folder_id', $parentId)->where('user_id', $userId)->first();
            if($folderPermission) {
               // If user has permission then assign same permission to upload file.
               $permissionRow = [
                  'view' => 1,
                  'create' => 0,
                  'edit' => 0,
                  'info' => 1,
                  'download' => 1,
                  'upload' => 0,
                  'user_id' => $this->encode(['id' => $userId]),
                  'folder_id' => null,
                  'delete' => 0,
                  'permission' => false,
                  'applychild' => false
               ];
               $this->updateUserFolderPermissions([$permissionRow], $this->encode(['id' => $fileId]), true, null);
            }
         }
      } 
   }

   /**
    * @desc Function to download a file
    */
   public function downloadFile($id) {
      $resource = FolderMaster::find($this->decode($id));
      if(!$resource)
         throw new Exception("File or Folder not found", Response::HTTP_NOT_FOUND);
         
      $path = $resource->path;

      if($resource->resource_type == FolderMaster::RESOURCE_TYPE_FOLDER) {
         $files = Storage::disk('s3')->allFiles($path);

         // Say so before the ZIP starts streaming, as errors can't be sent after that (and don't count it as a download).
         if(count($files) == 0) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, "$resource->name is empty, there is nothing to download.");
         }

         $resource->last_downloaded_at = now();
         $resource->times_downloaded = intval($resource->times_downloaded) + 1;
         $resource->save();

         header('Access-Control-Allow-Origin: *');
         header('Access-Control-Allow-Methods: *');
         header('Access-Control-Allow-Headers: *');

         // enable output of HTTP headers
         $options = new Archive;
         $options->setSendHttpHeaders(true);

         // create a new zipstream object
         $zip = new ZipStream($resource->name . '.zip', $options);

         foreach($files as $file) {
            // We do this because we want to the zip contents to be added from the folder name instead of from the beginning that is root
            // Means file -> root/medwinexe/file1, we need medwinexe/file1, so we do the below.
            $startZipPath = Str::after($file, $resource->name);
            $startZipPath = $resource->name . $startZipPath;            
            $stream = Storage::disk('s3')->getDriver()->readStream($file);
            $zip->addFileFromStream($startZipPath, $stream);
            fclose($stream);
         }
         $zip->finish();
      } else {
   
         try {
            DB::transaction(function() use($resource, $path) {
               
               $resource->last_downloaded_at = now();
               $resource->times_downloaded = intval($resource->times_downloaded) + 1;
               $resource->save();
               
               $url = Storage::disk('s3')->getClient()->getObjectUrl(config('filesystems.disks.s3.bucket'), $path);
               
               $stream = Storage::disk('s3')->getDriver()->readStream($path);
               
               $fsize = Storage::disk('s3')->size($path);
               
               $path_parts = pathinfo($url);
               
               if($path_parts['extension'] != 'txt') {
                  header('Access-Control-Allow-Origin: *');
               }
               header('Access-Control-Allow-Methods: *');
               header('Access-Control-Allow-Headers: *');
               header("Content-type: application/octet-stream");
               header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a file download
               header("Content-length: $fsize");
               header("Cache-control: private"); //use this to open files directly
               while(!feof($stream)) {
                     $buffer = fread($stream, 2048);
                     echo $buffer;
               }
               fclose($stream);
            });
         } catch(Exception $ex) {
            throw new Exception($ex->getMessage() ?: "Error in downloading files", intval($ex->getCode()) ?: Response::HTTP_INTERNAL_SERVER_ERROR);
         }
      }
   }

   // Get folder information
   public function getFolderInfo($id) {
      $id = $this->decode($id);
      $folder = FolderMaster::find($id);
      $size = 0;
      if(!$folder) 
         throw new Exception("Folder not found", Response::HTTP_NOT_FOUND);
   
      $path = $folder->path ? $folder->path : 'root';
      $folder->path = $path;

      if($folder->resource_type == FolderMaster::RESOURCE_TYPE_FOLDER) {
         $arr = [0];
         foreach(Storage::disk('s3')->allFiles($path) as $file) {
            $arr[] = Storage::disk('s3')->size($file);
         }
         // Size in MB 
         $folder->file_size = array_sum($arr);
         $numberOfFileFolder = [
            'files' => count(Storage::disk('s3')->allFiles($path)),
            'folders' => count(Storage::disk('s3')->allDirectories($path))
         ];
         $folder->children_count = json_encode($numberOfFileFolder);
      } else {
         $folder->file_size = Storage::disk('s3')->size($folder->path);
      }
      
      $folder->save();
      return $folder;
   }

   public function getUserFolderPermissions($id, $fromAdd = false) {

      $id = $this->decode($id);
      $data = [];
      // Prepare the data for setting the permissions for folder
      $superUserIds = User::role('superuser')->get(['id'])->toArray();

      // Extract all the user which are not superuser.
      $users = User::where('id', '<>', auth('sanctum')->user()->id)->whereNotIn('id', $superUserIds)->get();
      $data = [];
      $count = 1;
      foreach($users as $user) {
         
         $arr = [];
         $arr['selectAll'] = false;
         $arr["sno"] = $count++;
         // Assign a random id for now
         $arr["id"] = Str::random(32);
         $arr["folder_id"] = null;
         $arr["user_id"] = $this->encode(['id' => $user->id], 'id');
         $arr["name"] = $user->first_name . " " . $user->last_name;
         
         if($fromAdd) {
            foreach(FolderMaster::PERMISSION_KEYS as $key) {
               $arr[$key] = false;
               $arr["to_update"] = false;
            }
         } else {
            // Extract the permissions from the table and assign
            $folderPermissions = FolderPermission::where('folder_id', $id)->where('user_id', $user->id)
            ->first(['id', 'folder_id', 'view', 'create', 'edit', 'info', 'permission', 'delete', 'upload', 'download']);

            if(is_null($folderPermissions)) {
               foreach(FolderMaster::PERMISSION_KEYS as $key) {
                  $arr[$key] = false;
                  $arr["to_update"] = false;
               }
            } else {
               foreach(FolderMaster::PERMISSION_KEYS as $key) {
                  $tempArray = $folderPermissions->toArray();
                  $arr["to_update"] = true;
                  $arr["folder_id"] = $this->encode(['id' => $tempArray["folder_id"]]);
                  $arr["id"] = $this->encode(['id' => $tempArray["id"]]);
                  $arr[$key] = $tempArray[$key] == 1 ? true : false;
               }
            }
         }
         $arr['applychild'] = false;
         $data[] = $arr;
      } 
      // Prepare the data
      return $data;
   }

    /**
     * @desc Function to get the ids of desecendants of a node
     * @param FolderMaster $noide
     * @return void
     */
   public function deleteDescendantNodes($node) {
     $nodeChildren = FolderMaster::where('parent_id', $node->id)->get();
     if(count($nodeChildren) > 0) {
      foreach($nodeChildren as $nodeChild) {
         $this->deleteDescendantNodes($nodeChild);
      }
     } 
     $node->delete();
   } 

    /**
     * @desc Function to get the ids of desecendants of a node without the main node
     * @param FolderMaster $noide
     * @return void
     */
   public function deleteDescendantNodes_WithoutOriginFolder($node) {
     $nodeChildren = FolderMaster::where('parent_id', $node->id)->get();
     if(count($nodeChildren) > 0) {
      foreach($nodeChildren as $nodeChild) {
         $this->deleteDescendantNodes($nodeChild);
         $nodeChild->delete();
      }
     } 
   } 

    /**
     * Move up tree and update the size
     */
    public function resetFolderSizeUp($folder) {
      if($folder && $folder->resource_type == FolderMaster::RESOURCE_TYPE_FOLDER) {
         $path = $folder->path;
         $size = 0;
         $arr = [];
         foreach(Storage::disk('s3')->allFiles($path) as $file) {
            $arr[] = number_format(Storage::disk('s3')->size($file)/pow(1024, 2), 1);
         }

         $size = array_sum($arr);
         // Size in MB 

         $folder->file_size = $size;
         $folder->save();
         $parentFolder = FolderMaster::find($folder->parent_id);
         $this->resetFolderSizeUp($parentFolder);
      }
    }

    public function resetChildCountUp($folder) {
      if($folder && $folder->resource_type == FolderMaster::RESOURCE_TYPE_FOLDER) {
         $path = $folder->path;

         $childrenCount = json_decode($folder->children_count, true, 512);
         $childrenCount[FolderMaster::CHILD_FILES] =  count(Storage::disk('s3')->allFiles($path));
         $childrenCount[FolderMaster::CHILD_FOLDERS] =  count(Storage::disk('s3')->allDirectories($path));
         $folder->children_count = json_encode($childrenCount);
         $folder->save();
         $parentFolder = FolderMaster::find($folder->parent_id);
         $this->resetChildCountUp($parentFolder);
      }
   }

   public function updateLastTimeAndPermission($toUpdateFiles, $userId = null) {
      foreach($toUpdateFiles as $file) {
         $file->updated_at = now();
         $file->updated_by = $userId;
         $file->save();
         $this->createFilePermissionIfOwner($userId, $file->id, $file->parent_id);
      }
   }

   public function updateFileSize($toUpdateFiles, $userId = null) {
      foreach($toUpdateFiles as $file) {
         $file->file_size = number_format(Storage::disk('s3')->size($file->path)/pow(1024, 2), 1);
         $file->updated_at = now();
         $file->updated_by = $userId;
         $file->save();
      }
   }
}

