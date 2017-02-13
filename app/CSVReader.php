<?php
/*
 * @author: petereussen
 * @package: berlinger-test
 */

namespace App;

use App\Validators\EmptyHeaderColumnsException;
use League\Csv\Reader;

/**
 * Wrapper around CVSReader to clean up the source CSV as much as possible
 *
 * @package App
 */
class CSVReader
{
  /**
   * @var Reader
   */
  protected $reader;

  /**
   * CSVReader constructor.
   * @param string $file
   */
  public function __construct($file)
  {
    // Assume end user does not comply with normal standards
    if ( file_exists($file) ) {
      if (!ini_get("auto_detect_line_endings")) {
        ini_set("auto_detect_line_endings", '1');
      }

      $this->reader = Reader::createFromPath($file);
      $this->reader->setDelimiter('|');   // As per spec
    } else {
      throw new \BadMethodCallException('File not found ' . $file);
    }
  }

  /**
   * Read the header line from the CSV
   *
   * @return array
   */
  public function getHeader()
  {
    $header = $this->reader->fetchOne(0);
    $header = array_map('strtolower',$header);
    $header = array_map('trim',$header);

    $emptyHeaders = array_filter($header,'strlen');

    if ( count($emptyHeaders) != count($header) ) {
      throw new EmptyHeaderColumnsException("Your header contains empty elements");
    }
    return $header;
  }

  /**
   * Loop through all entries and call callback for all entries that comply with the header
   *
   * @param $callable
   */
  public function each($callable)
  {
    if ( !is_callable($callable)) {
      throw new \InvalidArgumentException(__METHOD__ . ' requires a callable method as parameter');
    }

    $header = $this->getHeader();
    $this->reader->setOffset(1);

    foreach( $this->reader as $row ) {

      // If they don't match/ it's an invalid row
      if ( count($row) === count($header)) {
        $row = array_map('trim',$row);
        $keyedRow = array_combine($header,$row);
        $callable($keyedRow);
      }
    }

  }
}