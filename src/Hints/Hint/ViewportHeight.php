<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Resolver\CookieBag;
use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;
use DevCoding\Hints\Base\CookieHintInterface;
use DevCoding\Hints\Base\CookieHintTrait;

/**
 * Returns an indication of the viewport's height, in pixels.  This hint is not part of an official specification or
 * draft proposal at this time.
 *
 * References:
 *  https://github.com/WICG/responsive-image-client-hints/blob/main/Viewport-Height-Explainer.md
 *
 * Class ViewportHeight
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class ViewportHeight extends Hint implements ConstantAwareInterface, CookieHintInterface
{
  use CookieHintTrait;

  const HEADER  = 'Sec-CH-Viewport-Height';
  const COOKIE  = 'vh';
  const DEFAULT = 768;
  const DRAFT   = false;
  const STATIC  = false;
  const VENDOR  = true;

  public function cookie(CookieBag $CookieBag)
  {
    $value = $CookieBag->resolve($this->config()->cookie);
    if (!isset($value))
    {
      if ($dh = (new Height())->cookie($CookieBag))
      {
        if ($dh < $this->default())
        {
          $value = $dh;
        }
      }
    }

    return $value;
  }
}
