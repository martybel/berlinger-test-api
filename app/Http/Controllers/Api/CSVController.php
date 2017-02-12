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
}