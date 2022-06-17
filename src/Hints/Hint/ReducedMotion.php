<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Dependency\CookieBagAwareInterface;
use DevCoding\Helper\Dependency\CookieBagTrait;
use DevCoding\Hints\Base\HeaderBagHint;

/**
 * Returns the value for the Prefers-Reduced-Motion secure client hint header, or polyfills the same. This indicates the
 * user's preference for reduced animation.
 *
 * References:
 *   https://web.dev/prefers-reduced-motion/
 *   https://wicg.github.io/user-preference-media-features-headers/#sec-ch-prefers-reduced-motion
 *
 * Class ReducedMotion
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 * @package DevCoding\Hints
 */
class ReducedMotion extends HeaderBagHint implements CookieBagAwareInterface
{
  use CookieBagTrait;

  const KEY     = 'Sec-CH-Prefers-Reduced-Motion';
  const COOKIE  = 'p.rm';
  const DEFAULT = false;

  /**
   * @return bool
   */
  public function get()
  {
    return $this->prefers(self::KEY) ?? $this->cookie(self::COOKIE) ?? $this->getDefault();
  }

  /**
   * @return bool
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
   * @return false
   */
  public function isStatic()
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
    return true;
  }
}
