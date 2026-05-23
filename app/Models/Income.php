<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = [
        'income_id',
        'date',
        'last_change_date',
        'warehouse_name',
        'nm_id',
        'quantity',
        'total_sum',
    ];
    //
}
