<?php

namespace DevCoding\Hints\Base;

use DevCoding\Client\Object\Headers\HeaderBag;
use DevCoding\Helper\Dependency\BrowserResolverTrait;
use DevCoding\Helper\Dependency\CookieBagAwareInterface;
use DevCoding\Helper\Dependency\CookieBagTrait;
use DevCoding\Helper\Dependency\FeatureBagAwareInterface;
use DevCoding\Helper\Dependency\FeatureBagTrait;
use DevCoding\Helper\Dependency\HeaderBagAwareInterface;
use DevCoding\Helper\Dependency\HeaderBagTrait;

abstract class AbstractFeature implements FeatureBagAwareInterface, HeaderBagAwareInterface, CookieBagAwareInterface
{
  use BrowserResolverTrait;
  use CookieBagTrait;
  use FeatureBagTrait;
  use HeaderBagTrait;

  /**
   * @return string
   */
  public static function key()
  {
    if (defined(static::KEY))
    {
      return static::KEY;
    }

    return '';
  }

  /**
   * @return bool|null
   */
  abstract public function is();

  /**
   * @return string|int|float|bool|null
   */
  abstract public function getDefault();

  /**
   * @return HeaderBag
   */
  protected function getHeaderBag()
  {
    return $this->_HeaderBag;
  }
}