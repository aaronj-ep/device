<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Hints\FeatureHints;

trait FeatureHintsTrait
{
  /** @var FeatureHints */
  protected $_FeatureHints = null;

  /**
   * @return FeatureHints
   */
  public function getFeatureHints(): FeatureHints
  {
    if (!isset($this->_FeatureHints))
    {
      $this->_FeatureHints = new FeatureHints();
    }

    return $this->_FeatureHints;
  }

  /**
   * @param FeatureHints $FeatureHints
   *
   * @return $this
   */
  public function setFeatureHints(FeatureHints $FeatureHints)
  {
    $this->_FeatureHints = $FeatureHints;

    return $this;
  }
}