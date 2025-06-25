<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAccessLog extends Model
 {
     protected $fillable = [
         'document_id',
         'user_id',
         'granted_by_user_id',
         'filename',
         'category',
         'size',
     ];

     public function document()
     {
         return $this->belongsTo(Document::class);
     }

     public function user()
     {
         return $this->belongsTo(User::user_id);
     }

     public function grantedBy()
     {
         return $this->belongsTo(User::class, 'granted_by_user_id');
     }
 }
