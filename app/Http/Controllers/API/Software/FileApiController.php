<?php

namespace App\Http\Controllers\API\Software;

use App\Http\Controllers\Controller;
use App\Models\CustomerBackup;
use App\Models\FolderMaster;
use App\Services\FolderService;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileApiController extends Controller
{
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

            // Handle file upload
            if($request->hasFile('uploadFile')) {
                $fileNameWithExt = $request->file('uploadFile')->getClientOriginalName();    

                // Check if backup service exists
                $backupCustomer = CustomerBackup::where('acctno', $request->cuserid)->where('active', 1)->first();
            
                if(!$backupCustomer)
                    return response(['success' => false, 'msg' => 'Folder Not Found or Inactive.'], Response::HTTP_NOT_FOUND); 

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
                                
                // Get just extension
                $extension = $request->file('uploadFile')->getClientOriginalExtension();

                // Check if file already exists or not
                $data = [];
                if($fileToUploadExists){
                    // First fetch the old size of the file.
                    $file = FolderMaster::where('name', $fileNameWithExt)->where('parent_id', $id)->first();
                    $pathToUpload = $folder->path . '/' . $fileNameWithExt;
                    $uploaded = Storage::disk('s3')->put($pathToUpload, fopen($request->uploadFile, 'r+'));
                    if(!$uploaded) {
                        return response(['success' => false, 'msg' => 'Error in uploading file while updating mode, please contact admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                    app(FolderService::class)->updateFileSize([$file]);
                    $data = ['msg' => 'Id of uploaded file','id' => $file->id];
                } else {
                    $slug = SlugService::createSlug(FolderMaster::class, 'slug', $fileName, ['unique' => true]);
                
                    $tempFile["parent_id"] = $folder->id;
                    $tempFile["name"] = $fileNameWithExt;
                    $tempFile["slug"] = $slug;
                    $tempFile["children_count"] = null;
                    $tempFile["depth"] = $folder->depth + 1;
                    $tempFile["path"] = $folder->path . '/' . $fileNameWithExt;
                    $tempFile["file_size"] = number_format(filesize($request->uploadFile)/pow(1024, 2), 1);
                    $tempFile["resource_type"] = FolderMaster::RESOURCE_TYPE_FILE;
                    $tempFile["resource_module"] = 'backup';
                    $tempFile["created_at"] = now();
                    $tempFile["created_by"] = null;
    
                    // File name to store
                    $pathToUpload = $folder->path . '/' . $fileNameWithExt;
                    $uploaded = Storage::disk('s3')->put($pathToUpload, fopen($request->uploadFile, 'r+'));
                    if(!$uploaded) {
                        return response(['success' => false, 'msg' => 'Error in upload backup file, creation. Contact admin'], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
    
                    $resource = FolderMaster::create($tempFile);
    
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
}
