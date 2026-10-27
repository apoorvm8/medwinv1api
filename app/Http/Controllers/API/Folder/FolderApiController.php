<?php

namespace App\Http\Controllers\API\Folder;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Folder\UploadFileRequest;
use App\Http\Resources\API\Folder\FolderResource;
use App\Http\Resources\API\Folder\FoldersResource;
use App\Services\FolderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FolderApiController extends Controller
{
    private $folderService;

    public function __construct(FolderService $folderService)
    {
        $this->folderService = $folderService;
    } 

    /**
     * @desc Function to fetch all folders or based on filter params
     * @type GET
     * @return JsonResponse
     */
    public function index(Request $request) {
        $data = $this->folderService->getAll($request->filters);
        return response(['success' => true, 'msg' => 'Folders retrieved', 'data' => [
            'folders' => new FoldersResource($data)
        ]]);
    }

    /**
     * @desc Function to list a folder, or search everything below it, for the folder browser
     * @type GET
     * @return JsonResponse
     */
    public function browse(Request $request) {
        $params = $request->validate([
            'folder' => ['nullable', 'string'],
            'search' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', 'in:all,folder,file'],
            'modified' => ['nullable', 'in:any,today,7d,30d,custom'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
            'area' => ['nullable', 'in:all,backup,stock,software'],
            'sort' => ['nullable', 'in:name,last_modified,size,downloads'],
            'dir' => ['nullable', 'in:asc,desc'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
        return response(['success' => true, 'msg' => 'Folder contents retrieved', 'data' => $this->folderService->browse($params)]);
    }

    /**
    * @desc Function to create folder
    * @type POST
    * @return JsonResponse
    */
    public function store(Request $request) {
        $this->folderService->authorize($request->input('parent_id'), 'create');
        $this->folderService->createFolder($request->input(), auth('sanctum')->user()->id);
        return response(['success' => true, 'msg' => 'Folder created successfully.', 'data' => []]);
    }


    /**
    * @desc Function to edit folder 
    * @type POST
    * @return JsonResponse
    */
    public function edit(Request $request) {
        $this->folderService->authorize($request->input('id'), 'edit');
        $folder = $this->folderService->editFolder($request->input());
        return response(['success' => true, 'msg' => 'Folder details edited successfully.', 'data' => ['folder' => new FolderResource($folder)]]);
    }

    /**
     * @desc Function to delete folder
     * @type DELETE
     * @return JsonResponse
     */
    public function deleteFolder(Request $request, $id) {
        $this->folderService->authorize($id, 'delete');
        $type = $this->folderService->deleteFolder($id, $request->shouldDeleteFolder);
        return response(['success' => true, 'msg' => "$type deleted successfully.", 'data' => []]);
    }

    /**
     * @desc Function to upload files in a folder
     * @type POST
     * @return JsonResponse
     */
    public function uploadFiles(UploadFileRequest $request) {
        $this->folderService->authorize($request->input('id'), 'upload');
        $this->folderService->uploadFiles($request->all(), auth('sanctum')->user()->id);
        return response(['success' => true, 'msg' => count($request->folderFiles) . " Files uploaded successfully.", 'data' => []]);
    }

    /**
     * @desc Function to download file
     * @type GET
     * @return void
     */
    public function downloadFile(Request $request, $id) {
        // Checked here, not in downloadFile(): the customer portal and the desktop software use it too.
        $this->folderService->authorize($id, 'download');
        $this->folderService->downloadFile($id);
        return response(['success' => true, 'msg' => 'File download started, check browser.', 'data' => []]);
    }

    /**
     * @desc Function to get the folder information
     */
    public function getFolderInfo(Request $request, $id) {
        $this->folderService->authorize($id, 'info');
        $data = $this->folderService->getFolderInfo($id);
        return response(['succcess' => true, 'msg' => 'Folder information retrieved successfully.', 'data' => [
            'folder' => new FolderResource($data)
        ]]);
    }
    
    /**
     * @desc Function to return the permissions of folder by users
     */
    public function getUserFolderPermissions(Request $request, $id) {
        $fromAdd = $request->fromadd == 'true';
        // Creating a folder lists the users on its parent folder, to set their permissions up front.
        $this->folderService->authorize($id, $fromAdd ? 'create' : 'permission');
        $data = $this->folderService->getUserFolderPermissions($id, $fromAdd);
        return response(['succcess' => true, 'msg' => 'Folder permissions by user retrieved successfully.', 'data' => [
            'permissions' => $data
        ]]);
    }

    /**
     * @desc Function to return the permissions of folder by users
     */
    public function updateUserFolderPermissions(Request $request, $id) {
        $this->folderService->authorize($id, 'permission');
        $data = $this->folderService->updateUserFolderPermissions($request->stateRows, $id, true, auth('sanctum')->user()->id);
        return response(['succcess' => true, 'msg' => 'Folder permissions updated successfully.', 'data' => [
            'permissions' => $data
        ]]);
    }
}
    