<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CSVReader;
use App\Models\Media;

/**
 * Command line wrapper to test the CSV import
 * @package App\Console\Commands
 */
class csv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:import {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'import file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file = $this->argument('file');

        $reader = new CSVReader($file);
        $reader->each([$this,'StoreMedia']);
    }


    public function storeMedia($mediaRecord)
    {
      // Only accept those with valid URL's
      if ( filter_var($mediaRecord['picture_url'],FILTER_VALIDATE_URL) ) {
        Media::createFromInput($mediaRecord);
      }
    }

}
