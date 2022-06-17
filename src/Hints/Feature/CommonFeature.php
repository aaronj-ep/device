<?php

namespace DevCoding\Hints\Feature;

use DevCoding\Hints\Base\AbstractFeature;

class CommonFeature extends AbstractFeature
{
  /** @var string */
  protected $key;

  /**
   * @param string $key
   */
  public function __construct(string $key)
  {
    $this->key = $key;
  }

  public function is()
  {
    return $this->header($this->key) ?? $this->feature($this->key) ?? $this->getDefault();
  }

  public function getDefault()
  {
    return true;
  }
}