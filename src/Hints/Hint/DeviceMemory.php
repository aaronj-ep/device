<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\HeaderBagHint;

/**
 * Returns the value for the Devic-Memory client hint header, or polyfills the same. This indicates the amount of
 * device RAM. The amount of device RAM can be used as a fingerprinting variable, so values for the header are
 * intentionally coarse to reduce the potential for its misuse.
 *
 * References:
 *  https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Device-Memory
 *
 * Class Model
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class DeviceMemory extends HeaderBagHint
{
  const KEY     = 'Device-Memory';
  const DEFAULT = '4';

  /**
   * @return string|null
   */
  public function get()
  {
    return $this->header(self::KEY) ?? $this->getDefault();
  }

  /**
   * @return string
   */
  public function getDefault()
  {
    return self::DEFAULT;
  }

  /**
   * @return bool
   */
  public function isNative()
  {
    return false;
  }

  /**
   * @return bool
   */
  public function isDraft()
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

  public function isStatic()
  {
    return true;
  }


}
