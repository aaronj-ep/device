<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\HeaderBagHint;

/**
 * Returns the value for the Sec-CH-UA-Arch client hint header, or polyfills the same. This indicates the device
 * architecture's instruction set.
 *
 * References:
 *  https://wicg.github.io/ua-client-hints/#sec-ch-ua-arch
 *  https://web.dev/user-agent-client-hints/
 *
 * Class Arch
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class Arch extends HeaderBagHint
{
  const KEY     = 'Sec-CH-UA-Arch';
  const DEFAULT = 'x86';

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
    return true;
  }

  /**
   * @return bool
   */
  public function isDraft()
  {
    return false;
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
