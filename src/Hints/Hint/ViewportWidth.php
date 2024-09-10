<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Resolver\CookieBag;
use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;
use DevCoding\Hints\Base\CookieHintInterface;
use DevCoding\Hints\Base\CookieHintTrait;

/**
 * Returns the value for the Viewport-Width client hint header, or polyfills the same.  This indicates the
 * width of the viewport, which may or may not match the width of the device.
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
 * @licence MIT (https://github.com/jonesiscoding/device/blob/main/LICENSE)
 *
 * @package DevCoding\Hints
 */
class ViewportWidth extends Hint implements ConstantAwareInterface, CookieHintInterface
{
  use CookieHintTrait;

  const DEFAULT    = 1024;
  const HEADER     = 'Sec-CH-Viewport-Width';
  const ALTERNATES = ['Viewport-Width'];
  const COOKIE     = 'vw';
  const DRAFT      = false;
  const STATIC     = false;
  const VENDOR     = false;

  public function cookie(CookieBag $CookieBag)
  {
    $value = $CookieBag->resolve($this->config()->cookie);
    if (!isset($value))
    {
      if ($dw = (new Width())->cookie($CookieBag))
      {
        if ($dw < $this->default())
        {
          $value = $dw;
        }
      }
    }

    return $value;
  }
}
