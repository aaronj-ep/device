<?php

namespace DevCoding\Device;

use DevCoding\Client\Object\Browser\Browser;
use DevCoding\Client\Object\Headers\UA;
use DevCoding\Client\Object\Headers\UAFullVersionList;
use DevCoding\Client\Object\Version\ClientVersion;
use DevCoding\CodeObject\Object\Base\BaseVersion;
use DevCoding\Hints\Hint\FullVersionList;
use DevCoding\Hints\Hint\ViewportWidth;
use DevCoding\Hints\Hint\ViewportHeight;
use DevCoding\Hints\Hint\UserAgent;

class Client extends DeviceChild
{
  /**
   * @return Browser
   */
  public function getBrowser()
  {
    return $this->ClientHints->browser();
  }

  /**
   * @return UAFullVersionList|null
   */
  public function getFullVersionList()
  {
    if ($fvl = $this->ClientHints->get(FullVersionList::HEADER))
    {
      return new UAFullVersionList($fvl);
    }

    return null;
  }

  /**
   * @return UA|null
   */
  public function getUserAgent()
  {
    if ($ua = $this->ClientHints->get(UserAgent::HEADER))
    {
      return new UA($ua);
    }

    return null;
  }

  /**
   * @return BaseVersion
   */
  public function getVersion()
  {
    if ($full = $this->getFullVersionList())
    {
      return $full->getVersion();
    }
    elseif ($ua = $this->getUserAgent())
    {
      return new ClientVersion($ua->getVersion());
    }

    return null;
  }

  /**
   * @return float|int
   */
  public function getViewportHeight()
  {
    return $this->ClientHints->get(ViewportHeight::HEADER);
  }

  /**
   * @return float|int
   */
  public function getViewportWidth()
  {
    return $this->ClientHints->get(ViewportWidth::HEADER);
  }

  /**
   * @param string $key
   *
   * @return bool
   */
  public function isSupported($key)
  {
    return $this->ClientHints->bool($key);
  }
}
