<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Media extends Model
{
    use UuidTrait;

    protected $table = 'media';

    protected $fillable = ['uuid','title','source','local','imported'];
    protected $guarded  = ['id','uuid'];
    protected $uuids    = ['uuid'];
    protected $hidden   = ['id'];

    public function meta()
    {
      return $this->hasMany('App\\Models\\MediaMeta','media_id','id');
    }

    /**
     * Creates a local copy of a remote file
     *
     */
    public function checkLocalCopy()
    {
      if ( !$this->imported && $this->status === null ) {
        $relative = 'public/images/' . $this->uuid . $this->getPathExtension($this->source);
        $full     = storage_path($relative);

        if (!is_dir(dirname($full))) {
          mkdir(dirname($full),true,0777);
        }

        $fp = fopen(storage_path($relative),'w');

        $ch = curl_init($this->source);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        curl_exec($ch);

        fclose($fp);

        if ( curl_errno($ch) === 0 ) {
          $this->imported = 1;
          $this->local    = $relative;
        }
        $this->status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->save();

        if ($this->status === 404) {
          // Remove bogus files
          unlink($full);
        }

        curl_close($ch);
      }
    }

    /**
     * Creates a new media record, or updates an existing one based on the title
     *
     * @param   array $input
     * @return  Media
     */
    static public function createFromInput($input)
    {
      try {

        $media = static::where('title',$input['picture_title'])->firstOrFail();

        if ( $media->source !== $input['picture_url']) {
          $media->soure    = $input['picture_url'];
          $media->imported = 0;
          $media->status   = null;
          $media->save();
        }
      } catch( ModelNotFoundException $e) {
        $media = static::create([
          'title'   => $input['picture_title'],
          'source'  => $input['picture_url']
        ]);
      }

      $media->checkLocalCopy();

      /*
       * Store all other meta key values
       */
      $otherFields = array_diff(array_keys($input),['picture_url','picture_title']);

      foreach( $otherFields as $fieldName ) {
        try {
          $meta = MediaMeta::where('media_id',$media->id)->where('meta_key',$fieldName)->firstOrFail();

          $meta->meta_value = $input[$fieldName];
          $meta->save();
        } catch (ModelNotFoundException $e) {
          MediaMeta::create([
            'media_id'  => $media->id,
            'meta_key'  => $fieldName,
            'meta_value'=> $input[$fieldName]
          ]);
        }
      }
      return $media;
    }

    /**
     * Create a dot<ext> string if the original file had one
     *
     * @param $path
     * @return string
     */
    protected function getPathExtension($path)
    {
      $ext = File::extension($path);

      if ( $ext ) {
        return '.' . $ext;
      }
      return '';
    }
}
