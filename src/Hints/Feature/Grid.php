<?php

namespace DevCoding\Hints\Feature;

use DevCoding\Hints\Base\AbstractFeature;

class Grid extends AbstractFeature
{
  const KEY = 'CF-CssGrid';

  /**
   * @return bool
   */
  public function is()
  {
    return $this->cookie('f.cdg') ?? $this->feature(self::KEY) ?? $this->getDefault();
  }

  /**
   * @return false
   */
  public function getDefault()
  {
    return false;
  }
}