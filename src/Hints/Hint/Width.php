<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Hints\Base\ConstantAwareInterface;
use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\CookieHintInterface;
use DevCoding\Hints\Base\CookieHintTrait;

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
class Width extends Hint implements CookieHintInterface, ConstantAwareInterface
{
  use CookieHintTrait;

  const HEADER     = 'Sec-CH-Width';
  const ALTERNATES = ['Width'];
  const COOKIE     = 'dw';
  const DEFAULT    = 1024;
  const DRAFT      = false;
  const STATIC     = true;
  const VENDOR     = false;

  public static function isMobile($value): bool
  {
    return $value <= 480;
  }
}
