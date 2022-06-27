<?php

namespace DevCoding\Hints\Base;

use DevCoding\Helper\Dependency\HeaderBagAwareInterface;
use DevCoding\Helper\Dependency\HeaderBagTrait;
use DevCoding\Hints\Hint\Platform;

abstract class HeaderBagHint extends AbstractHint implements HeaderBagAwareInterface
{
  use HeaderBagTrait;

  protected function isPlatformHint($platform)
  {
    return $platform == $this->header(Platform::KEY);
  }
}
