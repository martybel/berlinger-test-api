<?php

namespace App\Jobs;

use App\CSVReader;
use App\Models\Media;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class ProcessCSVJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $uuid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $csv    = storage_path('app/csv/'. $this->uuid . '.csv');

      if ( file_exists($csv) ) {
        $reader = new CSVReader($csv);
        $reader->each([$this,'StoreMedia']);

        unlink($csv);
      }
      return true;
    }

    public function storeMedia($mediaRecord)
    {
      // Only accept those with valid URL's
      if ( filter_var($mediaRecord['picture_url'],FILTER_VALIDATE_URL) ) {
        Media::createFromInput($mediaRecord);
      }
    }

}
