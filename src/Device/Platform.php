<?php

namespace DevCoding\Device;

use DevCoding\Client\Object\Platform\PlatformImmutable;
use DevCoding\Client\Object\Platform\PlatformInterface;
use DevCoding\CodeObject\Object\Base\BaseVersion;
use DevCoding\Helper\Dependency\ClientHintsAwareInterface;
use DevCoding\Helper\Dependency\ClientHintsTrait;
use DevCoding\Hints\ClientHints;

class Platform implements PlatformInterface, ClientHintsAwareInterface
{
  use ClientHintsTrait;

  /**
   * @return string|null
   */
  public function getArch()
  {
    return $this->getClientHints()->get(ClientHints::ARCH);
  }

  /**
   * @return int|string|null
   */
  public function getBitness()
  {
    return $this->getClientHints()->get(ClientHints::BITNESS);
  }

  /**
   * @return BaseVersion
   */
  public function getVersion(): BaseVersion
  {
    return $this->getObject()->getVersion();
  }

  /**
   * @return string
   */
  public function getPlatform()
  {
    return $this->getObject()->getPlatform();
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->getObject()->getPlatform();
  }

  /**
   * @return PlatformImmutable
   */
  protected function getObject()
  {
    return $this->getClientHints()->getPlatform();
  }
}