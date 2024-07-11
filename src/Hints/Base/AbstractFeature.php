<?php

namespace DevCoding\Hints\Base;

use DevCoding\Helper\Dependency\BrowserResolverAwareInterface;
use DevCoding\Helper\Dependency\BrowserResolverTrait;
use DevCoding\Helper\Dependency\ConfigBagAwareInterface;
use DevCoding\Helper\Dependency\ConfigBagTrait;
use DevCoding\Helper\Dependency\CookieBagAwareInterface;
use DevCoding\Helper\Dependency\CookieBagTrait;
use DevCoding\Helper\Dependency\FeatureResolverAwareInterface;
use DevCoding\Helper\Dependency\FeatureBagTrait;
use DevCoding\Helper\Dependency\HeaderBagAwareInterface;
use DevCoding\Helper\Dependency\HeaderBagTrait;

abstract class AbstractFeature implements FeatureResolverAwareInterface, HeaderBagAwareInterface, CookieBagAwareInterface
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