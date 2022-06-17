<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Dependency\CookieBagAwareInterface;
use DevCoding\Helper\Dependency\CookieBagTrait;
use DevCoding\Hints\Base\HeaderBagHint;

/**
 * Returns the value for the Width client hint header, or polyfills the same.  This indicates the device's width in
 * pixels.
 *
 * References:
 *   https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/client-hints#device_hints
 *   https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Width
 *
 * Class Width
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class Width extends HeaderBagHint implements CookieBagAwareInterface
{
  use CookieBagTrait;

  const KEY     = 'Sec-CH-Width';
  const COOKIE  = 'h.dw';
  const DEFAULT = 1024;

  public function get()
  {
    return $this->header([self::KEY, 'Width']) ?? $this->cookie(self::COOKIE) ?? $this->getDefault();
  }

  public function getDefault()
  {
    return self::DEFAULT;
  }

  public function isNative()
  {
    return true;
  }

  public function isVendor()
  {
    return false;
  }

  public function isDraft()
  {
    return false;
  }

  /**
   * @return bool
   */
  public function isStatic()
  {
    return true;
  }
}
