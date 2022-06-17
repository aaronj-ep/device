<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Dependency\CookieBagAwareInterface;
use DevCoding\Helper\Dependency\CookieBagTrait;
use DevCoding\Hints\Base\HeaderBagHint;

/**
 * Returns the value for the DPR client hint header, or polyfills the same.  This indicates the device pixel ratio.
 *
 * References:
 *   https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/DPR
 *   https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/client-hints#device_hints
 *
 * Class DPR
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class DPR extends HeaderBagHint implements CookieBagAwareInterface
{
  use CookieBagTrait;
  const KEY     = 'Sec-CH-DPR';
  const COOKIE  = 'h.dpr';
  const DEFAULT = 1;

  /**
   * @return float
   */
  public function get()
  {
    // TODO: Could be derrived from iOS version; 9.3.6 was the greatest OS that the iPad mini 1 (last non-retina) ran
    return $this->header([self::KEY, 'DPR']) ?? $this->cookie(self::COOKIE) ?? $this->getDefault();
  }

  /**
   * @return float
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
    return false;
  }
}
