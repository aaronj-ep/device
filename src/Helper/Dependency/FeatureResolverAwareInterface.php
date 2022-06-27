<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Helper\Resolver\FeatureResolver;

interface FeatureResolverAwareInterface extends BrowserResolverAwareInterface
{
  /**
   * @param string $key
   *
   * @return mixed
   */
  public function feature($key);

  /**
   * @param FeatureResolver $FeatureResolver
   *
   * @return FeatureResolverAwareInterface
   */
  public function setFeatureResolver(FeatureResolver $FeatureResolver);
}