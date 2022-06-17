<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Dependency\CookieBagAwareInterface;
use DevCoding\Helper\Dependency\CookieBagTrait;
use DevCoding\Hints\Base\HeaderBagHint;

/**
 * Returns the value for the DPR client hint header, or polyfills the same.  This indicates the device pixel ratio.
 *
 * References:
 *   https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/ECT
 *   https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/client-hints#device_hints
 *
 * Class ECT
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class ECT extends HeaderBagHint implements CookieBagAwareInterface
{
  use CookieBagTrait;

  const KEY     = 'ECT';
  const COOKIE  = 'h.ect';
  const DEFAULT = '4g';

  /**
   * @return string|null
   */
  public function get()
  {
    return $this->header(self::KEY) ?? $this->cookie(self::COOKIE) ?? $this->getDefault();
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
    return false;
  }


}
