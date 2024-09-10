<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Resolver\CookieBag;
use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Hints\Base\ReducedHintInterface;

/**
 * Returns the value for the Prefers-Reduced-Data secure client hint header, or polyfills the same. This indicates the
 * user's preference for using a reduced amount of data in each request.
 *
 * References:
 *   https://web.dev/user-preference-media-features-headers/
 *
 * Class ReducedData
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 * @package DevCoding\Hints
 */
class ReducedData extends SaveData implements ReducedHintInterface
{
  const HEADER     = 'Sec-CH-Prefers-Reduced-Data';
  const ALTERNATES = ['Save-Data'];
  const DRAFT      = true;
  const STATIC     = false;
  const VENDOR     = false;

  public function header(HeaderBag $HeaderBag, $additional = [])
  {
    $value = $HeaderBag->resolve($this->config()->header);
    if(isset($value))
    {
      return $value;
    }

    $reduced = $HeaderBag->resolve($this->config()->alternates);
    if (isset($reduced))
    {
      return $this->bool($reduced) ? self::REDUCE : self::NO_PREFERENCE;
    }

    if ($ect = (new ECT())->header($HeaderBag))
    {
      return ECT::isSlow($ect) ? self::REDUCE : self::NO_PREFERENCE;
    }

    return $this->isMetered($HeaderBag) ? self::REDUCE : null;
  }

  public function cookie(CookieBag $CookieBag)
  {
    $value = $CookieBag->resolve($this->config()->cookie);
    if (isset($value))
    {
      return $this->bool($value) ? self::REDUCE : self::NO_PREFERENCE;
    }

    if ($ect = (new ECT())->cookie($CookieBag))
    {
      return ECT::isSlow($ect) ? self::REDUCE : self::NO_PREFERENCE;
    }

    return null;
  }
}
