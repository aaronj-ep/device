<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;

/**
 * Returns the value for the Sec-UA-Prefers-Contrast secure client hint header, or polyfills the same. This indicates the
 * user's preference for contrast.
 *
 * References:
 *   https://web.dev/user-preference-media-features-headers/
 *   https://wicg.github.io/user-preference-media-features-headers/#sec-ch-prefers-contrast
 *
 * Class Contrast
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/main/LICENSE)
 *
 * @package DevCoding\Hints
 */
class Contrast extends Hint implements ConstantAwareInterface
{
  // Value Constants
  const LESS          = 'less';
  const MORE          = 'more';
  const NO_PREFERENCE = 'no-preference';

  // Config Constants
  const HEADER  = 'Sec-CH-Prefers-Contrast';
  const DEFAULT = self::NO_PREFERENCE;
  const DRAFT   = true;
  const STATIC  = false;
  const VENDOR  = false;
}
