<?php

namespace DevCoding\Device;

use DevCoding\Hints\ClientHints;

abstract class DeviceChild
{
  /** @var ClientHints */
  protected $ClientHints;

  /**
   * @param ClientHints $ClientHints
   */
  public function __construct(ClientHints $ClientHints)
  {
    $this->ClientHints = $ClientHints;
  }
}
