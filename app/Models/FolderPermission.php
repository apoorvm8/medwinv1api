<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolderPermission extends Model
{
    use HasFactory;

    protected $table = 'folder_permissions';
    protected $primaryKey = 'id';
    public $timestamps = false;    
    protected $dates = ['updated_at', 'created_at'];
    protected $fillable = [
        'folder_id', 'user_id', 'view', 'create', 'edit', 'info', 
        'permission', 'delete', 'upload', 'download', 
        'created_at', 'updated_at', 'created_by', 'updated_by'
    ];
    
    public function permissions() {
        return $this->belongsTo(FolderMaster::class);
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
}
