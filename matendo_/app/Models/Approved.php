<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approved extends Model
{
    //
protected $fillable = [
        'application_id',
        'reference_code',
        'first_name',
        'last_name',
        'profession',
        'approved_at',
    ];
}
