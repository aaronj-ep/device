<?php

namespace DevCoding\Hints\Feature;

use DevCoding\Hints\Base\AbstractFeature;

class ArrayIncludes extends AbstractFeature
{
  const KEY = 'CF-ArrayIncludes';

  public function is()
  {
    return $this->cookie('f.jai') ?? $this->feature(self::KEY) ?? $this->getDefault();
  }

  public function getDefault()
  {
    return false;
  }
}