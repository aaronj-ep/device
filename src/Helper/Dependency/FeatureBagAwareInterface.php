<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Helper\Resolver\FeatureResolver;

interface FeatureBagAwareInterface extends BrowserResolverAwareInterface
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
   * @return FeatureBagAwareInterface
   */
  public function setFeatureBag(FeatureResolver $FeatureResolver);
}