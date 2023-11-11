<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerStockAccess extends Model
{
    use HasFactory;
    protected $table = "customer_stock_access";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'acctno', 'active', 'install_date', 'next_amc_date', 'remarks', 'folder_id'
    ];

    // address is combination of subadd1, subadd2, subadd3
    public const SEARCHABLE = [
        'customer_stock_access.acctno', 'customer_stock_access.install_date', 'customer_stock_access.next_amc_date', 
        'customer_stock_access.remarks', 'customer_backups.created_at', 'customer_data.subdesc', 
        'customer_data.gstno'
    ];

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
