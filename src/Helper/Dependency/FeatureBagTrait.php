<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Helper\Resolver\FeatureResolver;

/**
 * Class FeatureBagTrait.php
 *
 * @package DevCoding\Hints\Base
 */
trait FeatureBagTrait
{
  /** @var FeatureResolver */
  protected $_FeatureResolver;

  /**
   * @param FeatureResolver $FeatureResolver
   *
   * @return $this
   */
  public function setFeatureBag(FeatureResolver $FeatureResolver)
  {
    $this->_FeatureResolver = $FeatureResolver;

    return $this;
  }

  public function feature($key)
  {
    return $this->getFeatureBag()->resolve($key);
  }

  /**
   * @return FeatureResolver
   */
  protected function getFeatureBag()
  {
    if (!isset($this->_FeatureResolver))
    {
      $this->_FeatureResolver = new FeatureResolver();
    }

    return $this->_FeatureResolver;
  }
}
