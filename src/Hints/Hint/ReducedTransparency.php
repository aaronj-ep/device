<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\HeaderBagHint;

/**
 * Returns the value for the Prefers-Reduced-Motion secure client hint header, or polyfills the same. This indicates the
 * user's preference for reduced animation.
 *
 * References:
 *   https://web.dev/user-preference-media-features-headers/
 *   https://wicg.github.io/user-preference-media-features-headers/#sec-ch-prefers-reduced-transparency
 *
 * Class ReducedMotion
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 * @package DevCoding\Hints
 */
class ReducedTransparency extends HeaderBagHint
{
  const KEY     = 'Sec-CH-Prefers-Reduced-Transparency';
  const DEFAULT = false;

  /**
   * @return bool
   */
  public function get()
  {
    return $this->prefers(self::KEY) ?? $this->getDefault();
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

  public function isStatic()
  {
    return false;
  }
}
