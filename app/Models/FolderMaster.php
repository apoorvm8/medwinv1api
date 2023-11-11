<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FolderMaster extends Model
{
    use HasFactory, Sluggable;
    protected $table = 'folder_masters';
    protected $primaryKey = 'id';
    public $timestamps = false;    
    protected $dates = ['last_downloaded_at', 'updated_at', 'created_at'];
    protected $fillable = [
        'parent_id', 'name', 'slug', 'children_count', 'depth', 'path', 
        'file_size', 'times_downloaded', 'resource_type', 'resource_module', 
        'created_at', 'updated_at', 'created_by', 'updated_by'
    ];

    public const CHILD_FILES = 'files';
    public const CHILD_FOLDERS = 'folders'; 
    public const RESOURCE_TYPE_FOLDER = "folder";
    public const RESOURCE_TYPE_FILE = "file";
    public const PERMISSION_KEYS = ['view', 'create', 'edit', 'info', 'permission', 'delete', 'upload', 'download'];
    // protected $appends = ['isRootParent'];

    static function getAll($filters) {
        $query = FolderMaster::query();
        foreach($filters as $key => $val) {
           $query->where($key, $val);
        }
        return $query->with(['folderPermissions' => function($query) {
            return $query->where('user_id', auth('sanctum')->user()->id);
        }])->get();
    }

     /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function folderPermissions() : HasMany {
        return $this->hasMany(FolderPermission::class, 'folder_id', 'id');
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // public function getIsRootParentAttribute() {
    //     $parentId = $this->parent_id;
    //     if($parentId) {
    //         if(FolderMaster::find($parentId)->slug == 'root') {
    //             return true;
    //         } else {
    //             return false;
    //         }
    //     } else {
    //         return true; // Means it is root itself
    //     }
    // }
}
