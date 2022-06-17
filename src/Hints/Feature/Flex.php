<?php

namespace DevCoding\Hints\Feature;

use DevCoding\Hints\Base\AbstractFeature;

class Flex extends AbstractFeature
{
  const KEY = 'CF-FlexBox';

  /**
   * @return bool
   */
  public function is()
  {
    return $this->cookie('f.cdf') ?? $this->feature(self::KEY) ?? $this->getDefault();
  }

  /**
   * @return false
   */
  public function getDefault()
  {
    return false;
  }
}