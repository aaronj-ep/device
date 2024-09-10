<?php

namespace DevCoding\Device;

use DevCoding\Hints\Hint\ECT;
use DevCoding\Hints\Hint\RemoteAddr;
use DevCoding\Hints\Hint\SaveData;

/**
 * Class Connection
 * @package DevCoding\Device
 */
class Connection extends DeviceChild
{
  /**
   * @return bool
   */
  public function getEffectiveType()
  {
    return $this->ClientHints->get(ECT::HEADER);
  }

  /**
   * @return string|null
   */
  public function getRemoteAddress()
  {
    return $this->ClientHints->get(RemoteAddr::HEADER);
  }

  /**
   * @return bool
   */
  public function isSaveData()
  {
    return $this->ClientHints->bool(SaveData::HEADER);
  }
}
