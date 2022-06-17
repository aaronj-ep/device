<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Hints\FeatureHints;

interface FeatureHintsAwareInterface
{
  /**
   * @return FeatureHints
   */
  public function getFeatureHints(): FeatureHints;

  /**
   * @param FeatureHints $FeatureHints
   *
   * @return $this
   */
  public function setFeatureHints(FeatureHints $FeatureHints);
}