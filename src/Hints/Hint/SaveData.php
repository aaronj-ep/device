<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Resolver\CookieBag;
use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Hints\Base\BooleanHintTrait;
use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;
use DevCoding\Hints\Base\CookieHintInterface;
use DevCoding\Hints\Base\CookieHintTrait;

/**
 * Returns the value for the Save-Data secure client hint header, or polyfills the same. This indicates the
 * user's preference for using a reduced amount of data in each request.
 *
 * References:
 *   https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Save-Data
 *   https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/save-data
 *
 * Class SaveData
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/main/LICENSE)
 * @package DevCoding\Hints
 */
class SaveData extends Hint implements ConstantAwareInterface, CookieHintInterface
{
  use CookieHintTrait;
  use BooleanHintTrait;

  const HEADER     = 'Save-Data';
  const ALTERNATES = [ReducedData::HEADER];
  const COOKIE     = 'sd';
  const DEFAULT    = false;
  const DRAFT      = false;
  const STATIC     = false;
  const VENDOR     = true;

  public function header(HeaderBag $HeaderBag, $additional = [])
  {
    $value = $HeaderBag->resolve($this->config()->header, $additional);
    if(isset($value))
    {
      return $value;
    }

    $reduced = $HeaderBag->resolve($this->config()->alternates);
    if (isset($reduced))
    {
      return $this->bool($reduced) ? 'on' : 'off';
    }

    if ($ect = (new ECT())->header($HeaderBag))
    {
      return ECT::isSlow($ect) ? 'on' : 'off';
    }

    return $this->isMetered($HeaderBag) ? 'on' : null;
  }

  public function cookie(CookieBag $CookieBag)
  {
    $value = $CookieBag->resolve($this->config()->cookie);
    if (isset($value))
    {
      return $this->bool($value) ? 'on' : 'off';
    }

    if ($ect = (new ECT())->cookie($CookieBag))
    {
      return ECT::isSlow($ect) ? 'on' : 'off';
    }

    return null;
  }

  /**
   * Checks seldom used headers to determine if a connection is likely to be metered.  It is used exclusively as a
   * backup hint source in the 'isSaveData' method.
   *
   * This method is private due to the large number of false positives it may give. If you are on cellular & your
   * provider adds these headers, that does not necesssarily imply that you are on a metered plan.  In fact, due to
   * evil marketing, being on an 'unlimited' cellular connection doesn't even really mean that you aren't metered.
   *
   * @return bool
   */
  protected function isMetered(HeaderBag $HeaderBag)
  {
    $m = ['X_WAP_PROFILE', 'ATT_DEVICEID', 'WAP_CONNECTION', 'X_ROAMING', 'X_MOBILE_UA', 'X_MOBILE_GATEWAY'];
    $h = $HeaderBag->resolve($m);

    return isset($h) ? true : null;
  }
}
