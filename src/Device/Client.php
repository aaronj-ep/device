<?php

namespace DevCoding\Device;

use DevCoding\CodeObject\Object\Base\BaseVersion;
use DevCoding\Helper\Dependency\BrowserResolverAwareInterface;
use DevCoding\Helper\Dependency\BrowserResolverTrait;
use DevCoding\Helper\Dependency\ClientHintsAwareInterface;
use DevCoding\Helper\Dependency\ClientHintsTrait;
use DevCoding\Helper\Dependency\FeatureHintsAwareInterface;
use DevCoding\Helper\Dependency\FeatureHintsTrait;
use DevCoding\Hints\ClientHints;

class Client implements ClientHintsAwareInterface, FeatureHintsAwareInterface, BrowserResolverAwareInterface
{
  use ClientHintsTrait;
  use FeatureHintsTrait;
  use BrowserResolverTrait;

  /**
   * @return string
   */
  public function getBrand()
  {
    return $this->getBrowserObject()->getBrand();
  }

  /**
   * @return string
   */
  public function getEngine()
  {
    return $this->getBrowserObject()->getEngine();
  }

  /**
   * @return BaseVersion
   */
  public function getVersion()
  {
    return $this->getBrowserObject()->getVersion();
  }

  /**
   * @return float|int
   */
  public function getViewportHeight()
  {
    return $this->getClientHints()->get(ClientHints::VIEWPORT_HEIGHT);
  }

  /**
   * @return float|int
   */
  public function getViewportWidth()
  {
    return $this->getClientHints()->get(ClientHints::VIEWPORT_WIDTH);
  }

  /**
   * @param string $key
   *
   * @return bool
   */
  public function isSupported($key)
  {
    return $this->getFeatureHints()->isSupported($key);
  }
}