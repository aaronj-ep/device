<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\BooleanHintTrait;
use DevCoding\Hints\Base\BooleanValueInterface;
use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;
use DevCoding\Hints\Base\CookieHintInterface;
use DevCoding\Hints\Base\CookieHintTrait;
use DevCoding\Hints\Base\ReducedHintInterface;

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
 * @licence MIT (https://github.com/jonesiscoding/device/blob/main/LICENSE)
 * @package DevCoding\Hints
 */
class ReducedMotion extends Hint implements ConstantAwareInterface, CookieHintInterface, BooleanValueInterface, ReducedHintInterface
{
  use CookieHintTrait;
  use BooleanHintTrait;

  // Config Constants
  const HEADER  = 'Sec-CH-Prefers-Reduced-Motion';
  const COOKIE  = 'rm';
  const DEFAULT = self::NO_PREFERENCE;
  const DRAFT   = true;
  const STATIC  = false;
  const VENDOR  = false;
}
