<?php
/*
 * @author: petereussen
 * @package: berlinger-test
 */

namespace App;

use League\Csv\Reader;


class CSVReader
{
  /**
   * @var Reader
   */
  protected $reader;

  public function __construct($file)
  {
    if ( file_exists($file) ) {
      if (!ini_get("auto_detect_line_endings")) {
        ini_set("auto_detect_line_endings", '1');
      }

      $this->reader = Reader::createFromPath($file);
      $this->reader->setDelimiter('|');
    } else {
      throw new \BadMethodCallException('File not found ' . $file);
    }
  }

  public function getHeader()
  {
    $header = $this->reader->fetchOne(0);
    $header = array_map('strtolower',$header);
    $header = array_map('trim',$header);
    return $header;
  }

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