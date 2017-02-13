<?php

namespace App\Http\Controllers\Api;

use \App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

/**
 * Api Endpoint for all Media related queries
 *
 * @package App\Http\Controllers\Api
 */
class MediaController extends Controller
{
  /**
   * Returns a list of all available media items
   * To avoid getting too big responses, all responses are chunked into 1000 records a time (default)
   * You can change this by using the offset and limit parameters.
   *
   * By default it will also only show pictures which were downloaded succesfully (HTTP 200). But you
   * can change this by using the status parameter.
   *
   * @param Request $request
   * @return \Illuminate\Contracts\Routing\ResponseFactory
   */
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

  /**
   * Displays the image/media file
   *
   * @param $uid
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
   */
  public function getOne($uid)
  {
    $media = $this->getMediaByIdentifier($uid);

    if ( $media ) {
      $path  = storage_path($media->local);

      if ( File::exists($path)) {
        $response = response(File::get($path),200);
        $response->header('Content-Type',File::mimeType($path));
        return $response;
      }
      abort(500);
    }
    abort(404);

  }

  /**
   * Get information based on the UUID or title
   *
   * @param $uid
   * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
   */
  public function getInfo($uid)
  {
    $media = $this->getMediaByIdentifier($uid);

    if ( $media ) {
      return response($this->getMediaInfo($media));
    }
    abort(404);
  }

  /**
   * Locates an media item based on the UUID or title
   * @param $id
   * @return mixed
   */
  protected function getMediaByIdentifier($id)
  {

    try {
      $media = Media::where('uuid', $id)->firstOrFail();
    } catch ( ModelNotFoundException $m) {
      $media = Media::where('title', $id)->first();
    }
    return $media;
  }

  /**
   * Get a media information response for a single item
   *
   * @param Media $media
   * @return array
   */
  protected function getMediaInfo(Media $media)
  {
    $data = [
      'picture_uuid'  => $media->uuid,
      'picture_title' => $media->title,
      'picture_url'   => $media->source,
    ];

    foreach ($media->meta()->get() as $meta ) {
      $data[$meta->meta_key] = $meta->meta_value;
    }
    return $data;
  }
}