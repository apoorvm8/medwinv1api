<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerWhatsapp extends Model
{
    use HasFactory;
    protected $table = 'customer_whatsapps';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'acctno', 'active', 'install_date', 'next_amc_date', 'remarks'
    ];

    // address is combination of subadd1, subadd2, subadd3
    public const SEARCHABLE = [
        'customer_whatsapps.acctno', 'customer_whatsapps.install_date', 'customer_whatsapps.next_amc_date', 'customer_whatsapps.remarks', 'customer_whatsapps.created_at',
        'customer_data.subdesc', 'customer_data.gstno'
    ];

    public function customer() : BelongsTo {
        return $this->belongsTo(CustomerData::class, 'acctno', 'id');
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
