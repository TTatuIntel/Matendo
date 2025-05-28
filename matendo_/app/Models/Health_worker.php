<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Health_worker extends Model
{
    use HasFactory;
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'user_id',
    'application_id',
    'name',
    'specialty',
    'resume',
    'license_doc',
    'certifications',
    'application_snapshot_pdf',
];
   
}
