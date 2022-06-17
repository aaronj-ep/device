<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Client\Object\Platform\PlatformImmutable;
use DevCoding\Helper\Resolver\PlatformResolver;

interface PlatformResolverAwareInterface
{
  /**
   * @return PlatformImmutable
   */
  public function getPlatformObject();

  /**
   * @param PlatformResolver $PlatformResolver
   *
   * @return PlatformResolverAwareInterface
   */
  public function setPlatformResolver(PlatformResolver $PlatformResolver);
}