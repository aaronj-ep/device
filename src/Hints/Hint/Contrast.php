<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\HeaderBagHint;

/**
 * Returns the value for the Sec-UA-Prefers-Contrast secure client hint header, or polyfills the same. This indicates the
 * user's preference for contrast.
 *
 * References:
 *   https://web.dev/user-preference-media-features-headers/
 *   https://wicg.github.io/user-preference-media-features-headers/#sec-ch-prefers-reduced-motion
 *
 * Class Contrast
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class Contrast extends HeaderBagHint
{
  const KEY           = 'Sec-CH-Prefers-Contrast';
  const DEFAULT       = 'no-preference';
  const LESS          = 'less';
  const MORE          = 'more';
  const NO_PREFERENCE = 'no-preference';

  /**
   * @return string
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
  public function isDraft()
  {
    return true;
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
}

