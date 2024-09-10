<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;
use DevCoding\Hints\Base\CookieHintInterface;
use DevCoding\Hints\Base\CookieHintTrait;

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
class Height extends Hint implements ConstantAwareInterface, CookieHintInterface
{
  use CookieHintTrait;

  const HEADER  = 'CH-Height';
  const COOKIE  = 'dh';
  const DEFAULT = 768;
  const DRAFT   = false;
  const STATIC  = true;
  const VENDOR  = true;
}
