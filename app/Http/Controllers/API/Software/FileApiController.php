<?php

namespace App\Http\Controllers\API\Software;

use App\Http\Controllers\Controller;
use App\Models\CustomerBackup;
use App\Models\CustomerData;
use App\Models\CustomerStockAccess;
use App\Models\FolderMaster;
use App\Models\User;
use App\Services\FolderService;
use App\Traits\HashIds;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileApiController extends Controller
{
    use HashIds;
        /**
     * Store a backup in storage
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function backupFileBySoft(Request $request) {
        try {      
            $errors = [];
            $validator = Validator::make($request->all(), [
                'uploadFile' => 'required|file|max:50000',
                'password' => 'required',
                'uploadSpace' => 'required',
                'cuserid' => 'required',
                'backupType' => 'required'
            ]);
            
    
            
            if($validator->fails()) {
                $failedRules = $validator->failed(); 
                
                if(isset($failedRules['uploadFile']['Required'])) {
                    $errors['uploadFile'] = "Please upload a file first.";
                }

                if(isset($failedRules['uploadFile']['File'])) {
                    $errors['uploadFile'] = "Please upload a proper file.";
                }

                if(isset($failedRules['uploadFile']['Max'])) {
                    $errors['uploadFile'] = "Size cannot exceed 50mb.";
                }

                if(isset($failedRules['password']['Required'])) {
                    $errors['password'] = "Not authorized to access resource";
                }

                if(isset($failedRules['uploadSpace']['Required'])) {
                    $errors['uploadSpace'] = "Upload space needs to be defined.";
                }

                if(isset($failedRules['cuserid']['Required'])) {
                    $errors['cuserid'] = "Cuserid is required.";
                }

                if(isset($failedRules['backupType']['Required'])) {
                    $errors['backupType'] = "Backup Type is required.";
                }
                return response()->json(['success' => false, 'errors' => $errors], 400);
            }
            
            if($request->input('password') != "aabavpv@1234") {
                $errors['password'] = "Invalid password provided";
                return response()->json(['success' => false, 'errors' => $errors], 400);
            }

            $customer = CustomerData::find($request->cuserid);
            if(!$customer)
                return response(['success' => false, 'msg' => 'Customer not found'], Response::HTTP_NOT_FOUND); 
            
            if($customer->activestatus == "N")
                return response(['success' => false, 'msg' => 'Customer is locked'], Response::HTTP_BAD_REQUEST);

            // Handle file upload
            if($request->hasFile('uploadFile')) {
                $fileNameWithExt = $request->file('uploadFile')->getClientOriginalName();    
                // Check if backup service exists
                $backupCustomer = CustomerBackup::where('acctno', $request->cuserid)->where('active', 1)->first();
            
                if(!$backupCustomer)
                    return response(['success' => false, 'msg' => 'Backup service not enabled or inactive for customer.'], Response::HTTP_NOT_FOUND); 

                // Check if customer folder is still present
                $customerFolder = FolderMaster::find($backupCustomer->folder_id);

                if(!$customerFolder)
                    return response(['success' => false, 'msg' => 'Folder Not Found'], Response::HTTP_NOT_FOUND); 

                
                $backupType = $request->backupType;

                // Check The Backup Type Get the appropriate folder in which file will be uploaded.
                if($backupType == 'currentyear')
                    $folder = FolderMaster::where('parent_id', $customerFolder->id)->where('name', 'currentyear')->where('depth', 3)->where('resource_type', FolderMaster::RESOURCE_TYPE_FOLDER)->first(); 
                else if($backupType == 'lastyear')
                    $folder = FolderMaster::where('parent_id', $customerFolder->id)->where('name', 'lastyear')->where('depth', 3)->where('resource_type', FolderMaster::RESOURCE_TYPE_FOLDER)->first(); 
                else
                    return response(['success' => false, 'msg' => 'Invalid backup type provided', 'errors' => []], Response::HTTP_BAD_REQUEST);
                
                if(!$folder)
                    return response(['success' => false, 'msg' => 'Folder Not Found'], Response::HTTP_NOT_FOUND); 

                // Check the count of children
                $fileLst = FolderMaster::where('parent_id', $folder->id)->where('depth', 4)->where('resource_type', FolderMaster::RESOURCE_TYPE_FILE)->orderby('created_at', 'asc')->get();
                $parentPath = (!$folder->path ? "root" : $folder->path);
                
                // Variable to check if file exists in s3 or not
                $fileToUploadExists = Storage::disk('s3')->exists($parentPath . '/' . $fileNameWithExt);

                // If the file being uploaded is not present in backup folder (current or last) and count is 3 then we remove the oldest backup
                if(!$fileToUploadExists && count($fileLst) == 3) {
                    // Delete the oldest file 
                    $oldestFile = $fileLst[0];
                    $deleted = Storage::disk('s3')->delete($oldestFile->path);
                    if(!$deleted) {
                        return response(['success' => false, 'msg' => 'Error in deleting oldest backup file. Please contact web admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
                     }
                     
                    app(FolderService::class)->resetChildCountUp(FolderMaster::find($oldestFile->parent_id));
                    app(FolderService::class)->resetFolderSizeUp(FolderMaster::find($oldestFile->parent_id));
                    $oldestFile->delete();
                }

                $folder = $folder->refresh();
                
                $id = $folder->id;
                
                // Get just filename
                $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

                // Check if file already exists or not
                $data = [];
                if($fileToUploadExists){
                    // First fetch the old size of the file.
                    $file = FolderMaster::where('name', $fileNameWithExt)->where('parent_id', $id)->first();
                    $pathToUpload = $parentPath . '/' . $fileNameWithExt;
                    $uploaded = Storage::disk('s3')->put($pathToUpload, fopen($request->uploadFile, 'r+'));
                    if(!$uploaded) {
                        return response(['success' => false, 'msg' => 'Error in uploading file while updating mode, please contact admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                    app(FolderService::class)->updateFileSize([$file], null);
                    $data = ['msg' => 'Id of uploaded file','id' => $file->id];
                } else {
                    $slug = SlugService::createSlug(FolderMaster::class, 'slug', $fileName, ['unique' => true]);
                
                    $tempFile["parent_id"] = $folder->id;
                    $tempFile["name"] = $fileNameWithExt;
                    $tempFile["slug"] = $slug;
                    $tempFile["children_count"] = null;
                    $tempFile["depth"] = $folder->depth + 1;
                    $tempFile["path"] = $parentPath . '/' . $fileNameWithExt;
                    $tempFile["file_size"] = number_format(filesize($request->uploadFile)/pow(1024, 2), 1);
                    $tempFile["resource_type"] = FolderMaster::RESOURCE_TYPE_FILE;
                    $tempFile["resource_module"] = 'backup';
                    $tempFile["created_at"] = now();
                    $tempFile["created_by"] = null;
    
                    // File name to store
                    $pathToUpload = $parentPath . '/' . $fileNameWithExt;
                    $uploaded = Storage::disk('s3')->put($pathToUpload, fopen($request->uploadFile, 'r+'));
                    if(!$uploaded) {
                        return response(['success' => false, 'msg' => 'Error in upload backup file, creation. Contact admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
    
                    $resource = FolderMaster::create($tempFile);
                    // Get email id of shrawan jaiswal
                    $user = User::where('email', config('app.mainuseremail'))->first();
                    if($user) {
                        // Create Folder Permission for this file 
                        $permissionRow = [
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
                        ];
                        app(FolderService::class)->updateUserFolderPermissions([$permissionRow], $this->encode(['id' => $resource->id]), true, null);
                    }
                    
                    $data = ['msg' => 'Id of uploaded file', 'id' => $resource->id];
                }
                
                app(FolderService::class)->resetFolderSizeUp($folder);
                app(FolderService::class)->resetChildCountUp($folder);
                return response()->json(['success' => true, 'msg' => 'File uploaded successfully.', 'data' => $data], Response::HTTP_OK);               
            } else {
                return response()->json(['success' => false, 'msg' => 'Please provide a file to upload'], 401);
            }           
        } catch(Exception $e) {
            return response()->json(['success' => false, 'msg' => 'An error occured please try again.'], 500);
        }
    }

    public function downloadBySoft(Request $request) {
        $path = $request->path;
        if(!$path) {
           return response()->json(['success' => false, 'msg' => "Invalid path specified"], Response::HTTP_BAD_REQUEST);
        }

        $resource = FolderMaster::where('path', $path)->first();
        if(!$resource) 
            return response()->json(['success' => false, 'msg' => "Resource not found"], Response::HTTP_NOT_FOUND);

        app(FolderService::class)->downloadFile($this->encode(['id' => $resource->id]));
    }

    public function downloadBySoftOther(Request $request) {
        $errors = [];
        $validator = Validator::make($request->all(), [
            'path' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            $failedRules = $validator->failed(); 
            
            if(isset($failedRules['path']['Required'])) {
                $errors['path'] = "Path Is Required.";
            }

            if(isset($failedRules['password']['Required'])) {
                $errors['password'] = "Not authorized to access resource";
            }  

            return response()->json(['success' => false, 'errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        if($request->input('password') != "aabavpv@1234") {
            $errors['password'] = "Invalid password provided";
            return response()->json(['success' => false, 'errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $path = $request->path;
         // Check If folder type is specified
         if($request->has("type")) {
            if($request->type == 0 || $request->type == 1) {
                // If folder, then take out the extension
                if($request->type == 0) {
                    $pathArray = explode(".", $path);
                    $path = $pathArray[0];
                }
            } else {
                $errors['type'] = "Invalid type provided";
                return response()->json(['success' => false, 'errors' => $errors], Response::HTTP_BAD_REQUEST);
            }
        }

        $resource = FolderMaster::where('path', $path)->first();
        if(!$resource) 
            return response()->json(['success' => false, 'msg' => "Resource not found"], Response::HTTP_NOT_FOUND);

        app(FolderService::class)->downloadFile($this->encode(['id' => $resource->id]));
    }

    public function uploadBySoft(Request $request) {
        try {
            $errors = [];
            $validator = Validator::make($request->all(), [
                'uploadFile' => 'required',
                'password' => 'required',
                'id' => 'required',
                'depth' => 'required',
            ]);
            
            if($validator->fails()) {
                $failedRules = $validator->failed(); 
                
                if(isset($failedRules['uploadFile']['Required'])) {
                    $errors['uploadFile'] = "Please upload a file first.";
                }

                if(isset($failedRules['password']['Required'])) {
                    $errors['password'] = "Not authorized to access resource";
                }

                if(isset($failedRules['id']['Required'])) {
                    $errors['id'] = "ID field is required.";
                }

                if(isset($failedRules['depth']['Required'])) {
                    $errors['depth'] = "Depth field is required.";
                }
                
                return response()->json(['success' => false, 'errors' => $errors], Response::HTTP_BAD_REQUEST);
            }
            
            if($request->input('password') != "aabavpv@1234") {
                $errors['password'] = "Invalid password provided";
                return response()->json(['success' => false, 'errors' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Begin file upload
            if($request->hasFile('uploadFile')) {
                $fileNameWithExt = $request->file('uploadFile')->getClientOriginalName();    
              
                $id = 0;
               
                if($request->input('depth') == 0) {
                    $id = 1;
                } else {
                    // Since this will come from software, we are not decrypting the id
                    $id = $request->id;
                }
                // Check if customer folder is still present
                $folder = FolderMaster::find($id);

                if(!$folder)
                    return response(['success' => false, 'msg' => 'Folder Not Found'], Response::HTTP_NOT_FOUND); 

                $parentPath = (!$folder->path ? "root" : $folder->path);

 
                // Variable to check if file exists in s3 or not
                $fileToUploadExists = Storage::disk('s3')->exists($parentPath . '/' . $fileNameWithExt);
                
                // Get just filename
                $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

                // Check if file already exists or not
                $data = [];
                if($fileToUploadExists){
                    // First fetch the old size of the file.
                    $file = FolderMaster::where('name', $fileNameWithExt)->where('parent_id', $id)->first();
                    $pathToUpload = $parentPath . '/' . $fileNameWithExt;
                    $uploaded = Storage::disk('s3')->put($pathToUpload, fopen($request->uploadFile, 'r+'));
                    if(!$uploaded) {
                        return response(['success' => false, 'msg' => 'Error in uploading file while updating mode, please contact admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                    app(FolderService::class)->updateFileSize([$file], null);
                    $data = ['msg' => 'Id of uploaded file','id' => $file->id];
                } else {
                    $slug = SlugService::createSlug(FolderMaster::class, 'slug', $fileName, ['unique' => true]);
                
                    $tempFile["parent_id"] = $folder->id;
                    $tempFile["name"] = $fileNameWithExt;
                    $tempFile["slug"] = $slug;
                    $tempFile["children_count"] = null;
                    $tempFile["depth"] = $folder->depth + 1;
                    $tempFile["path"] = $parentPath . '/' . $fileNameWithExt;
                    $tempFile["file_size"] = number_format(filesize($request->uploadFile)/pow(1024, 2), 1);
                    $tempFile["resource_type"] = FolderMaster::RESOURCE_TYPE_FILE;
                    $tempFile["resource_module"] = 'backup';
                    $tempFile["created_at"] = now();
                    $tempFile["created_by"] = null;
    
                    // File name to store
                    $pathToUpload = $parentPath . '/' . $fileNameWithExt;
                    $uploaded = Storage::disk('s3')->put($pathToUpload, fopen($request->uploadFile, 'r+'));
                    if(!$uploaded) {
                        return response(['success' => false, 'msg' => 'Error in upload backup file, creation. Contact admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
    
                    $resource = FolderMaster::create($tempFile);

                    // Get email id of shrawan jaiswal
                    $user = User::where('email', config('app.mainuseremail'))->first();
                    if($user) {
                        // Create Folder Permission for this file 
                        $permissionRow = [
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
                        ];
                        app(FolderService::class)->updateUserFolderPermissions([$permissionRow], $this->encode(['id' => $resource->id]), true, null);
                    }
    
                    $data = ['msg' => 'Id of uploaded file', 'id' => $resource->id];
                }

                app(FolderService::class)->resetFolderSizeUp($folder);
                app(FolderService::class)->resetChildCountUp($folder);
                return response()->json(['success' => true, 'msg' => 'File uploaded successfully.', 'data' => $data], Response::HTTP_OK);               
            } else {
                return response()->json(['success' => false, 'msg' => 'Please provide a file to upload'], 401);
            }  
        } catch(Exception $ex) {
            return response()->json(['success' => false, 'msg' => 'An error occured please try again.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function uploadStockBySoft(Request $request) {
        try {
            $errors = [];
            $validator = Validator::make($request->all(), [
                'uploadFile' => 'required|file|max:10000',
                'password' => 'required',
                'uploadSpace' => 'required',
                'cuserid' => 'required',
            ]);

            if($validator->fails()) {
                $failedRules = $validator->failed(); 
                
                if(isset($failedRules['uploadFile']['Required'])) {
                    $errors['uploadFile'] = "Please upload a file first.";
                }

                if(isset($failedRules['uploadFile']['File'])) {
                    $errors['uploadFile'] = "Please upload a proper file.";
                }

                if(isset($failedRules['uploadFile']['Max'])) {
                    $errors['uploadFile'] = "Size cannot exceed 10mb.";
                }

                if(isset($failedRules['password']['Required'])) {
                    $errors['password'] = "Not authorized to access resource";
                }

                if(isset($failedRules['uploadSpace']['Required'])) {
                    $errors['uploadSpace'] = "Upload space needs to be defined.";
                }

                if(isset($failedRules['cuserid']['Required'])) {
                    $errors['cuserid'] = "Cuserid is required.";
                }

                return response()->json(['success' => false, 'errors' => $errors], Response::HTTP_BAD_REQUEST);
            }
            
            if($request->input('password') != "aabavpv@1234") {
                $errors['password'] = "Invalid password provided";
                return response()->json(['success' => false, 'errors' => $errors], Response::HTTP_BAD_REQUEST);
            }

            $customer = CustomerData::find($request->cuserid);
            if(!$customer)
                return response(['success' => false, 'msg' => 'Customer not found'], Response::HTTP_NOT_FOUND); 
            
            if($customer->activestatus == "N")
                return response(['success' => false, 'msg' => 'Customer is locked'], Response::HTTP_BAD_REQUEST);
            
            if($request->hasFile('uploadFile')) {
                $fileNameWithExt = $request->file('uploadFile')->getClientOriginalName();    

                // Check if backup service exists
                $stockCustomer = CustomerStockAccess::where('acctno', $request->cuserid)->where('active', 1)->first();
            
                if(!$stockCustomer)
                    return response(['success' => false, 'msg' => 'Stock access service not enabled for customer.'], Response::HTTP_NOT_FOUND); 

                // Check if customer folder is still present
                $folder = FolderMaster::find($stockCustomer->folder_id);

                if(!$folder)
                    return response(['success' => false, 'msg' => 'Folder Not Found'], Response::HTTP_NOT_FOUND); 

                // Variable to check if file exists in s3 or not
                $parentPath = (!$folder->path ? "root" : $folder->path);

                $fileToUploadExists = Storage::disk('s3')->exists($parentPath . '/' . $fileNameWithExt);
                
                // Get just filename
                $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

                // Check if file already exists or not
                $data = [];
                if($fileToUploadExists){
                    // First fetch the old size of the file.
                    $file = FolderMaster::where('name', $fileNameWithExt)->where('parent_id', $folder->id)->first();
                    $pathToUpload = $parentPath . '/' . $fileNameWithExt;
                    $uploaded = Storage::disk('s3')->put($pathToUpload, fopen($request->uploadFile, 'r+'));
                    if(!$uploaded) {
                        return response(['success' => false, 'msg' => 'Error in uploading file while updating mode, please contact admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                    app(FolderService::class)->updateFileSize([$file], null);
                    $data = ['msg' => 'Id of uploaded file','id' => $file->id];
                } else {
                    $slug = SlugService::createSlug(FolderMaster::class, 'slug', $fileName, ['unique' => true]);
                
                    $tempFile["parent_id"] = $folder->id;
                    $tempFile["name"] = $fileNameWithExt;
                    $tempFile["slug"] = $slug;
                    $tempFile["children_count"] = null;
                    $tempFile["depth"] = $folder->depth + 1;
                    $tempFile["path"] = $parentPath . '/' . $fileNameWithExt;
                    $tempFile["file_size"] = number_format(filesize($request->uploadFile)/pow(1024, 2), 1);
                    $tempFile["resource_type"] = FolderMaster::RESOURCE_TYPE_FILE;
                    $tempFile["resource_module"] = 'backup';
                    $tempFile["created_at"] = now();
                    $tempFile["created_by"] = null;
    
                    // File name to store
                    $pathToUpload = $parentPath . '/' . $fileNameWithExt;
                    $uploaded = Storage::disk('s3')->put($pathToUpload, fopen($request->uploadFile, 'r+'));
                    if(!$uploaded) {
                        return response(['success' => false, 'msg' => 'Error in upload backup file, creation. Contact admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
    
                    $resource = FolderMaster::create($tempFile);

                    // Get email id of shrawan jaiswal
                    $user = User::where('email', config('app.mainuseremail'))->first();
                    if($user) {
                        // Create Folder Permission for this file 
                        $permissionRow = [
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
                        ];
                        app(FolderService::class)->updateUserFolderPermissions([$permissionRow], $this->encode(['id' => $resource->id]), true, null);
                    }
    
                    $data = ['msg' => 'Id of uploaded file', 'id' => $resource->id];
                }

                app(FolderService::class)->resetFolderSizeUp($folder);
                app(FolderService::class)->resetChildCountUp($folder);
                return response()->json(['success' => true, 'msg' => 'File uploaded successfully.', 'data' => $data], Response::HTTP_OK);   
            } else {
                return response()->json(['success' => false, 'msg' => 'Please provide a file to upload'], 401);
            }  
        } catch(Exception $ex) {
            return response()->json(['success' => false, 'msg' => 'An error occured please try again.'], 500);
        }
    }

    public function downloadStockBySoft(Request $request) {
        Validator::make($request->all(), [
            'password' => 'required',
            'cuserid' => 'required|exists:customer_data,acctno',
            'outletid' => 'required|exists:customer_data,acctno',
            'filename' => 'required'
        ])->validate();

        // Begin the algo process:-
        $cuserid = $request->cuserid;
        $outletid = $request->outletid;
        $fileName = $request->filename;
        $fileName = substr($fileName, 1); // remove slash, since it is coming from software

        $customer = CustomerData::find($cuserid);
        $outlet = CustomerData::find($outletid);
        
        if($customer->activestatus == "N")
            return response(['success' => false, 'msg' => 'Customer is locked'], Response::HTTP_BAD_REQUEST);
            
        if($outlet->activestatus == "N")
            return response(['success' => false, 'msg' => 'Outlet Customer is locked'], Response::HTTP_BAD_REQUEST);

        $customerStockAllowed = CustomerStockAccess::where('acctno', $cuserid)->where('active', 1)->first();
        $outletStockAllowed = CustomerStockAccess::where('acctno', $outletid)->where('active', 1)->first();

        if(!$customerStockAllowed) 
            return response(['success' => false, 'msg' => 'Customer not allowed for stock access'], Response::HTTP_BAD_REQUEST);
        
        if(!$outletStockAllowed) 
            return response(['success' => false, 'msg' => 'Outlet not allowed for stock access'], Response::HTTP_BAD_REQUEST);
        
        $customerFolder = FolderMaster::find($customerStockAllowed->folder_id);
        $outletFolder = FolderMaster::find($outletStockAllowed->folder_id);
        
        if(!$customerFolder)
            return response(['success' => false, 'msg' => 'Customer Folder Not Found'], Response::HTTP_NOT_FOUND); 

        if(!$outletFolder)
            return response(['success' => false, 'msg' => 'Outlet Folder Not Found'], Response::HTTP_NOT_FOUND); 

        // Get the actual file, we will keep the name as folder only
        $folder = FolderMaster::where('name', $fileName)->where('depth', 3)->where('resource_type', FolderMaster::RESOURCE_TYPE_FILE)->where('parent_id', $outletFolder->id)->first();

        if(!$folder)
            return response(['success' => false, 'msg' => 'Outlet Stock File Not Found'], Response::HTTP_NOT_FOUND); 

        app(FolderService::class)->downloadFile($this->encode(['id' => $folder->id]));
    }
}
