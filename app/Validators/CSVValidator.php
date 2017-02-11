<?php

namespace App\Validators;

use App\CSVReader;

class CSVValidator
{
  protected $csv;
  protected $mandatory_fields = [
    'picture_title',
    'picture_url',
  ];

  public function __construct($file)
  {
    $this->csv = $file;
  }

  public function validHeader()
  {
    $reader = new CSVReader($this->csv);
    return $this->inMatch($this->mandatory_fields,$reader->getHeader());
  }

  protected function inMatch($matching,$containing)
  {
    $theSame = array_intersect($matching,$containing);

    return count($theSame) === count($matching);
  }
}