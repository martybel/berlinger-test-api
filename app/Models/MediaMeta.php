<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaMeta extends Model
{
    protected $table = 'media_meta';
    protected $fillable = ['meta_key', 'meta_value', 'media_id'];
    protected $hidden   = ['id','media_id'];

    public function media()
    {
      return $this->belongsTo('App\\Models\\Media','id','media_id');
    }
}
