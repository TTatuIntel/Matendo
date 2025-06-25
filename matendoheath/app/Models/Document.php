<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id',
        'filename',
        'path',
        'size',
        'category',
        'mime_type'
    ];

    protected $appends = ['download_url', 'view_url'];

    public function getDownloadUrlAttribute()
    {
        return route('documents.download', $this->id);
    }

    public function getViewUrlAttribute()
    {
        return route('documents.view', $this->id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
