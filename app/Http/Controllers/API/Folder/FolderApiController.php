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
    * @desc Function to create folder 
    * @type POST
    * @return JsonResponse
    */
    public function store(Request $request) {
        $this->folderService->createFolder($request->input(), auth('sanctum')->user()->id);
        return response(['success' => true, 'msg' => 'Folder created successfully.', 'data' => []]);
    }


    /**
    * @desc Function to edit folder 
    * @type POST
    * @return JsonResponse
    */
    public function edit(Request $request) {
        $folder = $this->folderService->editFolder($request->input());
        return response(['success' => true, 'msg' => 'Folder details edited successfully.', 'data' => ['folder' => new FolderResource($folder)]]);
    }

    /**
     * @desc Function to delete folder
     * @type DELETE
     * @return JsonResponse
     */
    public function deleteFolder(Request $request, $id) {
        $type = $this->folderService->deleteFolder($id, $request->shouldDeleteFolder);
        return response(['success' => true, 'msg' => "$type deleted successfully.", 'data' => []]);
    }

    /**
     * @desc Function to upload files in a folder
     * @type POST
     * @return JsonResponse
     */
    public function uploadFiles(UploadFileRequest $request) {
        $this->folderService->uploadFiles($request->all(), auth('sanctum')->user()->id);
        return response(['success' => true, 'msg' => count($request->folderFiles) . " Files uploaded successfully.", 'data' => []]);
    }

    /**
     * @desc Function to download file
     * @type GET
     * @return void
     */
    public function downloadFile(Request $request, $id) {
        $this->folderService->downloadFile($id);
        return response(['success' => true, 'msg' => 'File download started, check browser.', 'data' => []]);
    }

    /**
     * @desc Function to get the folder information
     */
    public function getFolderInfo(Request $request, $id) {
        $data = $this->folderService->getFolderInfo($id);
        return response(['succcess' => true, 'msg' => 'Folder information retrieved successfully.', 'data' => [
            'folder' => new FolderResource($data)
        ]]);
    }
    
    /**
     * @desc Function to return the permissions of folder by users
     */
    public function getUserFolderPermissions(Request $request, $id) {
        $data = $this->folderService->getUserFolderPermissions($id, $request->fromadd == 'true' ? true : false);
        return response(['succcess' => true, 'msg' => 'Folder permissions by user retrieved successfully.', 'data' => [
            'permissions' => $data
        ]]);
    }

    /**
     * @desc Function to return the permissions of folder by users
     */
    public function updateUserFolderPermissions(Request $request, $id) {
        $data = $this->folderService->updateUserFolderPermissions($request->stateRows, $id, true);
        return response(['succcess' => true, 'msg' => 'Folder permissions updated successfully.', 'data' => [
            'permissions' => $data
        ]]);
    }
}
    