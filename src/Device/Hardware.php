<?php

namespace DevCoding\Device;

use DevCoding\Client\Object\Hardware\Pointer;
use DevCoding\Helper\Dependency\ClientHintsAwareInterface;
use DevCoding\Helper\Dependency\ClientHintsTrait;
use DevCoding\Hints\ClientHints;

class Hardware implements ClientHintsAwareInterface
{
  use ClientHintsTrait;

  public function getDeviceMemory()
  {
    return $this->getClientHints()->get(ClientHints::DEVICE_MEMORY);
  }

  /**
   * @return float|int
   */
  public function getDevicePixelRatio()
  {
    return $this->getClientHints()->get(ClientHints::DPR);
  }

  /**
   * @return string
   */
  public function getEffectiveConnectionType()
  {
    return $this->getClientHints()->get(ClientHints::ECT);
  }

  /**
   * @return float|int
   */
  public function getHeight()
  {
    return $this->getClientHints()->get(ClientHints::HEIGHT);
  }

  /**
   * @return Pointer
   */
  public function getPointer()
  {
    return $this->getClientHints()->getPointer();
  }

  /**
   * @return string|null
   */
  public function getRemoteAddress()
  {
    return $this->getClientHints()->getRemoteAddress();
  }

  /**
   * @return float|int
   */
  public function getWidth()
  {
    return $this->getClientHints()->get(ClientHints::WIDTH);
  }
}
