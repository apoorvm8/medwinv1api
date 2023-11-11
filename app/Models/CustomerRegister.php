<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerRegister extends Model
{
    use HasFactory;
    protected $dates = ['datetimereg'];


    protected $fillable = [
        'acctno', 'subdesc', 'datetimereg','remarks', 'created_at', 'updated_at'
    ];

    // address is combination of subadd1, subadd2, subadd3
    public const SEARCHABLE = [
        'customer_registers.acctno', 'customer_registers.subdesc',
        'customer_registers.datetimereg', 'customer_registers.remarks'
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
