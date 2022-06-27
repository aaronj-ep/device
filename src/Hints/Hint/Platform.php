<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Dependency\PlatformResolverAwareInterface;
use DevCoding\Hints\Base\HeaderBagHint;
use DevCoding\Helper\Dependency\PlatformResolverTrait;
use DevCoding\Helper\Resolver\HeaderBag;

/**
 * Returns the value for the Sec-CH-UA-Platform client hint header, or polyfills the same. This is intended to indicate
 * the platform or operating system of the device.
 *
 * References:
 *   https://wicg.github.io/ua-client-hints/#sec-ch-ua-platform
 *   https://web.dev/user-agent-client-hints/
 *
 * Class Platform
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class Platform extends HeaderBagHint implements PlatformResolverAwareInterface
{
  use PlatformResolverTrait;

  const KEY     = 'Sec-CH-UA-Platform';
  const DEFAULT = 'Unknown';

  /**
   * @return string
   */
  public function get()
  {
    $header = $this->header(self::KEY);
    if (!isset($header) && $obj = $this->getObject())
    {
      $header = $obj->getPlatform();
    }

    return $header ?? $this->getDefault();
  }

  /**
   * @return string
   */
  public function getDefault()
  {
    return self::DEFAULT;
  }

  public function getObject()
  {
    return $this->getPlatformObject();
  }

  /**
   * @return bool
   */
  public function isNative()
  {
    return true;
  }

  /**
   * @return bool
   */
  public function isVendor()
  {
    return false;
  }

  /**
   * @return bool
   */
  public function isDraft()
  {
    return false;
  }

  /**
   * @return HeaderBag
   */
  protected function getHeaderBag()
  {
    return $this->_HeaderBag;
  }

  public function isStatic()
  {
    return true;
  }
}
