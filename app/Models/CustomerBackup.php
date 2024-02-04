<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerBackup extends Model
{
    use HasFactory;
    protected $table = "customer_backups";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'acctno', 'active', 'install_date', 'next_amc_date', 'remarks', 'folder_id', 'number_of_backup'
    ];

    // address is combination of subadd1, subadd2, subadd3
    public const SEARCHABLE = [
        'customer_backups.acctno', 'customer_backups.install_date', 'customer_backups.next_amc_date', 
        'customer_backups.number_of_backup', 'customer_backups.remarks', 'customer_backups.created_at', 
        'customer_data.subdesc', 'customer_data.gstno'
    ];

    protected $appends = ['hasSetPassword'];

    public function customer() : BelongsTo {
        return $this->belongsTo(CustomerData::class, 'acctno', 'acctno');
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

    public function getHasSetPasswordAttribute() {
        $customerData = CustomerData::find($this->acctno);
        if($customerData) {
            if($customerData->password) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

