<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Dependency\CookieBagAwareInterface;
use DevCoding\Helper\Dependency\CookieBagTrait;
use DevCoding\Hints\Base\HeaderBagHint;

/**
 * Returns the value for the DPR client hint header, or polyfills the same.  This indicates the device pixel ratio.
 *
 * References:
 *   https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/client-hints#device_hints
 *   https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Viewport-Width
 *
 * Class ViewportWidth
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class ViewportWidth extends HeaderBagHint implements CookieBagAwareInterface
{
  use CookieBagTrait;

  const DEFAULT = 1024;
  const KEY     = 'Sec-CH-Viewport-Width';
  const COOKIE  = 'h.vw';

  public function get()
  {
    return $this->header([self::KEY, 'Viewport-Width']) ?? $this->cookie(self::COOKIE) ?? $this->getDefault();
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

  public function isStatic()
  {
    return false;
  }
}
