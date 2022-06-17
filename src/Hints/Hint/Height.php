<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Dependency\CookieBagAwareInterface;
use DevCoding\Helper\Dependency\CookieBagTrait;
use DevCoding\Hints\Base\HeaderBagHint;

/**
 * Returns an indication of the device's height, in pixels.  This hint is not part of an official specification or
 * draft proposal at this time.
 *
 * Class Height
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class Height extends HeaderBagHint implements CookieBagAwareInterface
{
  use CookieBagTrait;

  const KEY     = 'CH-Height';
  const COOKIE  = 'h.dh';
  const DEFAULT = 768;

  /**
   * @return string|int
   */
  public function get()
  {
    return $this->cookie(self::COOKIE) ?? $this->getDefault();
  }

  /**
   * @return int
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

  public function isStatic()
  {
    return true;
  }
}
