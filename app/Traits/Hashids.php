<?php

namespace App\Traits;

use Exception;
use Hashids\Hashids as HashidsHashids;

trait HashIds {
    public function decode($val, $type="Data") {
        $hashIds = new HashidsHashids('', config('app.hash_min_length'));
        $arr = $hashIds->decode($val);
        if(count($arr) > 0) {
            return $arr[0];
        } else {
            throw new Exception("$type with this id not found.");
        }
    }
    
    public function encode($val, $key = "id") {
        $hashIds = new HashidsHashids('', config('app.hash_min_length'));
        return $hashIds->encode($val[$key]);
    }
}