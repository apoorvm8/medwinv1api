<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;


class CustomerData extends Authenticatable
{
    use HasFactory;
    protected $guard = 'customer'; // telling the model which gaurd to use
    protected $table = 'customer_data';
    protected $primaryKey = 'acctno';
    public $timestamps = false;    
    protected $dates = ['created_at', 'updated_at'];

    protected $fillable = [
        'acctno', 'subdesc', 'subadd1',
        'subadd2','subadd3','subpercn',
        'subphone','gstno','area',
        'state', 'interstate', 'importsw',
        'softwaretype', 'installdate', 'nextamcdate',
        'amcamount', 'recvamount', 'activestatus',
        'acctcode', 'sub3code', 'email', 'created_at', 'updated_at', 'narration',
        'softref', 'password'
    ];

    // address is combination of subadd1, subadd2, subadd3
    public const SEARCHABLE = [
        'acctno', 'subdesc', 'address', 'installdate', 'nextamcdate', 'subphone', 'amcamount',
        'narration', 'area', 'state', 'softref'
    ];

    public function eInvoice() : HasOne {
        return $this->hasOne(Einvoice::class, 'acctno', 'acctno');
    }

    public function customerBackup(): HasOne {
        return $this->hasOne(CustomerBackup::class, 'acctno', 'acctno');
    }

    public function customerStockAccess(): HasOne {
        return $this->hasOne(CustomerStockAccess::class, 'acctno', 'acctno');
    }

    public function customerWhatsapp(): HasOne {
        return $this->hasOne(CustomerWhatsapp::class, 'acctno', 'acctno');
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
