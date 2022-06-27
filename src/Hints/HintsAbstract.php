<?php

namespace DevCoding\Hints;

use DevCoding\Helper\Dependency\BrowserResolverTrait;
use DevCoding\Helper\Dependency\CookieBagAwareInterface;
use DevCoding\Helper\Dependency\CookieBagTrait;
use DevCoding\Helper\Dependency\DependencyTrait;
use DevCoding\Helper\Dependency\FeatureResolverAwareInterface;
use DevCoding\Helper\Dependency\FeatureBagTrait;
use DevCoding\Helper\Dependency\HeaderBagAwareInterface;
use DevCoding\Helper\Dependency\HeaderBagTrait;

abstract class HintsAbstract implements CookieBagAwareInterface, HeaderBagAwareInterface, FeatureResolverAwareInterface
{
  use DependencyTrait;
  use CookieBagTrait;
  use HeaderBagTrait;
  use FeatureBagTrait;
  use BrowserResolverTrait;

  /**
   * Returns the value of the given environment variable, if set, or NULL.
   *
   * @param string $id the key for the environment variable, in uppercase
   *
   * @return string|int|float|bool|null the value of the environment variable
   */
  public static function getenv($id)
  {
    return $_ENV[$id] ?? $_SERVER[$id] ?? null;
  }
}
