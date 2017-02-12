<?php

namespace App\Validators;

use App\CSVReader;

/**
 * Validator to check contents of the CSV
 *
 * @package App\Validators
 */
class CSVValidator
{
  /**
   * The CSV file that is being checked
   *
   * @var string
   */
  protected $csv;

  /**
   * List of all mandatory header fields
   * @var array
   */
  protected $mandatory_fields = [
    'picture_title',
    'picture_url',
  ];

  public function __construct($file)
  {
    $this->csv = $file;
  }

  /**
   * Checks if the mandatory fields are present in the CSV
   *
   * @return bool
   */
  public function validHeader()
  {
    $reader = new CSVReader($this->csv);
    return $this->inMatch($this->mandatory_fields,$reader->getHeader());
  }

  /**
   * Checks if all elemens in $matching are present in $containing
   *
   * @param  array $matching
   * @param  array $containing
   * @return bool
   */
  protected function inMatch($matching,$containing)
  {
    $theSame = array_intersect($matching,$containing);

    return count($theSame) === count($matching);
  }
}