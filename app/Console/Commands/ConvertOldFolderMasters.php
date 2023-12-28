<?php

namespace App\Console\Commands;

use App\Models\CustomerBackup;
use App\Models\CustomerData;
use App\Models\CustomerStockAccess;
use App\Models\FolderMaster;
use App\Models\OldFolderMaster;
use App\Services\FolderService;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ConvertOldFolderMasters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foldermasters:convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert old folder masters into new version';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
     
        // Loop through old folder masters table
        $oldFolders = OldFolderMaster::orderBy('id', 'asc')->get();
        $notFoundCount = 0;
        foreach($oldFolders as $oldFolder) {
            // Check if resource exists in cloud from the given path based on type
            $path = $oldFolder->path ? $oldFolder->path : 'root';
            if($path != 'root') {
                // Create entry in folder masters
                $parentId = 0;
                $slug = SlugService::createSlug(FolderMaster::class, 'slug', $oldFolder->name, ['unique' => true]);
                $resourceModule = 'software';
                $resourceType = FolderMaster::RESOURCE_TYPE_FOLDER;

                if($oldFolder->type == 1) {
                    $resourceType = FolderMaster::RESOURCE_TYPE_FILE;
                }

                if($oldFolder->depth == 1) {
                    $parentId = FolderMaster::where('slug', 'root')->first()->id;
                } else {
                    // Find the parent id 
                    $parentPath = dirname($oldFolder->path);
                    // Check if folder with this parent path exists, should always be true
                    $parentFolder = FolderMaster::where('path', $parentPath)->first();
                    if($parentFolder) {
                        $parentId = $parentFolder->id;
                        if($parentFolder->name == 'customersbackup' || $parentFolder->name == 'currentyear' || $parentFolder->name == 'lastyear') {
                            $resourceModule = 'backup';
                        }

                        if($parentFolder->name == 'customerstock') {
                            $resourceModule = "stock";
                        }
                    }
                }

                if($oldFolder->name == 'customersbackup') {
                    $resourceModule = 'backup';
                }

                if($oldFolder->name == 'customerstock') {
                    $resourceModule = 'stock';
                }

                // Make sure this folder dosen't already exists
                if(!FolderMaster::where('path', $path)->first()) {
                    FolderMaster::create([
                        'parent_id' => $parentId, 'name' => $oldFolder->name, 'slug' => $slug, 'depth' => $oldFolder->depth,
                        'path' => $oldFolder->path, 'resource_type' => $resourceType,
                        'resource_module' => $resourceModule, 'created_at' => now(),
                    ]);

                    // Additional check to see if file is present in cloud or not
                    if(!Storage::disk('s3')->exists($path)) {
                        $notFoundCount++;
                        if($oldFolder->type == 0) {
                            $this->info('FOLDER NOT FOUND' . ' ' . $oldFolder->name);
                            Storage::disk('s3')->makeDirectory($path);
                            $this->info('FOLDER CREATED' . ' ' . $oldFolder->name);
                        } else {
                            $this->info('FILE NOT FOUND' . ' ' . $oldFolder->name);
                            Storage::disk('s3')->put($oldFolder->path, fopen(Storage::path($oldFolder->path), 'r+'));
                        }
                    }
                    $this->info($oldFolder->name . " synced. Not found count: $notFoundCount");
                } else {
                    $this->info($oldFolder->name . " already created");
                }
            }
        }

        $this->info('NOW STARTING FILE SIZE SYNC');
        // Now Sync the individual file size
        $newFiles = FolderMaster::where('resource_type', FolderMaster::RESOURCE_TYPE_FILE)->get();
        foreach($newFiles as $newFile) {
            $newFile->file_size = number_format(Storage::disk('s3')->size($newFile->path) / pow(1024, 2), 1);
            $newFile->save();
            $this->info("FILE: $newFile->name synced, size: $newFile->file_size MB");
        }


        $this->info('NOW STARTING FOLDER SIZE SYNC');
        // Now Sync the individual file size
        $newFolders = FolderMaster::where('resource_type', FolderMaster::RESOURCE_TYPE_FOLDER)->get();
        foreach($newFolders as $newFolder) {
            $this->info('STARTING update of size and child count of' . " " . $newFolder->name);
            $path = $newFolder->path ? $newFolder->path : 'root';

            $childrenCount = json_decode($newFolder->children_count, true, 512);
            $childrenCount[FolderMaster::CHILD_FILES] =  count(Storage::disk('s3')->allFiles($path));
            $childrenCount[FolderMaster::CHILD_FOLDERS] =  count(Storage::disk('s3')->allDirectories($path));
            $newFolder->children_count = json_encode($childrenCount);
            $newFolder->save();

            $size = 0;
            $arr = [];
            foreach(Storage::disk('s3')->allFiles($path) as $file) {
                $arr[] = number_format(Storage::disk('s3')->size($file)/pow(1024, 2), 1);
             }
    
             $size = array_sum($arr);
    
             $newFolder->file_size = $size;
             $newFolder->save();

            $this->info('FINISHED update of size and child count of' . " " . $newFolder->name);

            $parentFolder = FolderMaster::find($newFolder->parent_id);
            if($parentFolder) {
                // sync backup folders with backup tabke
                if($parentFolder->name == 'customersbackup') {
                    $arr = explode('_', $newFolder->name); // c2115_backup_name => [c2115,backup,name]
                    $str = $arr[0];
                    $cId = substr($str, 1);
                    $customerData = CustomerData::find($cId);
                    if($customerData) {
                        // Create the backup record
                        if(!CustomerBackup::where('acctno', $cId)->first()) {
                            $this->info('Creating customer backup ' . $newFolder->name . " $cId");
                            CustomerBackup::create([
                                'acctno' => $cId, 'folder_id' => $newFolder->id, 'active' => 1,
                                'number_of_backup' => json_encode(['currentyear' => 3, 'lastyear' => 3]),
                                'created_at' => now()
                            ]);
                        }
                    }
                }

                // sync stock folders with stock table
                if($parentFolder->name == 'customerstock') {
                    $arr = explode('_', $newFolder->name); // c2115_backup_name => [c2115,backup,name]
                    $str = $arr[0];
                    $cId = substr($str, 1);
                    $customerData = CustomerData::find($cId);
                    if($customerData) {
                        // Create the backup record
                        if(!CustomerStockAccess::where('acctno', $cId)->first()) {
                            $this->info('Creating customer stockaccess ' . $newFolder->name . " $cId");
                            CustomerStockAccess::create([
                                'acctno' => $cId, 'folder_id' => $newFolder->id, 'active' => 1,
                                'created_at' => now()
                            ]);
                        }
                    }
                }
            }
        }
     
    }
}
