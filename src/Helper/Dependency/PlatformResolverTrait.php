<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Client\Object\Platform\PlatformImmutable as PlatformObject;
use DevCoding\Helper\Resolver\PlatformResolver;

/**
 * Trait PlatformResolverTrait
 *
 * @package DevCoding\Helper\Dependency
 */
trait PlatformResolverTrait
{
  /** @var PlatformResolver */
  protected $_PlatformResolver;

  /**
   * @return PlatformObject|null
   */
  public function getPlatformObject()
  {
    return $this->_PlatformResolver->getPlatform();
  }

  /**
   * @param PlatformResolver $PlatformResolver
   *
   * @return $this
   */
  public function setPlatformResolver(PlatformResolver $PlatformResolver)
  {
    $this->_PlatformResolver = $PlatformResolver;

    return $this;
  }
}
