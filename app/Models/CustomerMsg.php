<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerMsg extends Model
{
    use HasFactory;
    protected $table = 'customer_msgs';

    protected $fillable = [
        'name', 'mobile_no', 'email', 'type_of_soft', 'seen', 'seen_by', 'created_at', 'updated_at'
    ];

    public const SEARCHABLE = [
        'customer_msgs.name', 'customer_msgs.mobile_no', 'customer_msgs.email', 'customer_msgs.type_of_soft',
        'customer_msgs.seen_by', 'customer_msgs.created_at',
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
