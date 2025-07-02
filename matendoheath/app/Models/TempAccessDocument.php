<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempAccessDocument extends Model
{
    use HasFactory;

    protected $table = 'temp_access_documents';

    protected $fillable = [
        'user_id',
        'granted_by_id',
        'filename',
        'path',
        'category',
        'size',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by_id');
    }
}
