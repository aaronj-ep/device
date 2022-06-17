<?php

namespace DevCoding\Hints\Feature;

use DevCoding\Hints\Base\AbstractFeature;

class SrcSet extends AbstractFeature
{
  const KEY = 'CF-SrcSet';

  public function is()
  {
    return $this->cookie('f.iss') ?? $this->feature(self::KEY) ?? $this->getDefault();
  }

  public function getDefault()
  {
    return false;
  }
}