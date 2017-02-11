<?php

namespace App\Http\Controllers\Api;

use \App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\MediaMeta;

class MediaController extends Controller
{
  public function getAll(Request $request)
  {
    $offset = $request->input('offset',0);
    $limit  = $request->input('limit',1000);
    $status = $request->input('status',200);

    $rows   = Media::select('id','uuid','title','source')
      ->where('status',$status)
      ->skip($offset)
      ->take($limit)
      ->get();
    $result = [];

    foreach( $rows as $row ) {
      $result[] = $this->getMediaInfo($row);
    }

    return response($result);
  }

  public function getOne($uid)
  {
    try {
      $media = Media::where('uuid',$uid)->firstOrFail();
      $path  = storage_path($media->local);

      if ( File::exists($path)) {
        $response = response(File::get($path),200);
        $response->header('Content-Type',File::mimeType($path));
        return $response;
      }
      abort(500);

    } catch (ModelNotFoundException $m) {
    }
    abort(404);
  }

  public function getInfo($uid)
  {
    try {
      $media = Media::where('uuid', $uid)->firstOrFail();

      return response($this->getMediaInfo($media));
    } catch (ModelNotFoundException $m) {
      abort(404);
    }
  }

  protected function getMediaInfo(Media $media)
  {
    $data = [
      'uuid'  => $media->uuid,
      'title' => $media->title,
      'url'   => $media->source,
    ];

    foreach ($media->meta()->get() as $meta ) {
      $data[$meta->meta_key] = $meta->meta_value;
    }
    return $data;
  }
}