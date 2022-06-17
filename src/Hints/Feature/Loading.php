<?php

namespace DevCoding\Hints\Feature;

use DevCoding\Hints\Base\AbstractFeature;

class Loading extends AbstractFeature
{
  const KEY = 'CF-Lazyload';

  public function is()
  {
    return $this->cookie('f.il') ?? $this->feature(self::KEY) ?? $this->getDefault();
  }

  public function getDefault()
  {
    return false;
  }
}