<?php
/*
 * @author: petereussen
 * @package: berlinger-test
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Jobs\ProcessCSVJob;
use App\Validators\CSVValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;

/**
 * CSV API Calls
 *
 * @package App\Http\Controllers\Api
 */
class CSVController extends Controller
{
  public function upload(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'csv' => 'max:' . $this->getMaxUploadSize()
    ]);

    if ($validator->fails()) {
      return response(['error' => 'Your file is too large.', 'error_code' => 'uploadtoolarge'],413);
    }

    $file = $request->file('csv');

    if ( $file ) {
      $validator = new CSVValidator($file->getPathname());

      if ( $validator->validHeader() ) {
        // Store file with a UUID to avoid filename conflicts
        $csvTarget = Uuid::generate()->string;

        $request->csv->storeAs('csv',$csvTarget . '.csv');
        $this->dispatch(new ProcessCSVJob($csvTarget));
        return response(['batch' => $csvTarget],200);
      } else {
        return response(['error' => 'Missing required columns', 'error_code' => 'fieldsnotfound'],400);
      }
    }

    return response(['error' => 'No file uploaded', 'error_code' => 'missingfile'],400);
  }

  protected function getMaxUploadSize()
  {
    $size = ini_get('upload_max_filesize');

    $short = strtoupper(substr($size,-1));

    switch ($short) {
      case 'G': $size = (int)$size * 1024;
      case 'M': $size = (int)$size * 1024;
      default:
        $size = (int)$size;
    }

    return $size;
  }
}