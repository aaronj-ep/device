<?php

namespace DevCoding\Hints\Feature;

use DevCoding\Hints\Base\AbstractFeature;

class LoadingLazyAttr extends AbstractFeature
{
  const KEY = 'CF-LoadingLazyAttr';

  public function is()
  {
    return $this->cookie('f.il') ?? $this->feature(self::KEY) ?? $this->getDefault();
  }

  public function getDefault()
  {
    return false;
  }
}