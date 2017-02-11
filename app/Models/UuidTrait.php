<?php
/*
 * @author: petereussen
 * @package: peilstok-peter
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid;

trait UuidTrait
{
  protected static function boot()
  {
    parent::boot();

    static::creating(function($model) {
      foreach( $model->getUUIDFields() as $field ) {
        $model->{$field} = Uuid::generate()->string;
      }
    });
  }

  protected function getUUIDFields()
  {
    if ( isset($this->uuids))
      return $this->uuids;
    return [];
  }

  public function uuid()
  {
    if ( isset($this->uuids[0]) ) {
      return $this->{$this->uuids[0]};
    }
    return $this->{$this->getKeyName()};
  }

  /**
   * Finds a Model based on UUID string
   * By default this method will use the first UUID in the uuids attribute. But you
   * can always pinpoint using the second argument
   *
   * @param string      $val
   * @param null|string $field
   * @return Model
   */
  static public function getByUUID($val,$field = null)
  {
    if ( $field === null ) {
      $model = new static();
      $uuids = $model->getUUIDFields();

      if ( $uuids ) {
        $field = array_shift($uuids);
      } else {
        $field = 'id';  // This will always fail, i know
      }
    }

    return static::where($field,$val)->first();
  }
}