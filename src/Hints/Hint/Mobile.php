<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Client\Resolver\Platform\AndroidMatcher;
use DevCoding\Client\Resolver\Platform\IosMatcher;
use DevCoding\Client\Resolver\Platform\WindowsMobileMatcher;
use DevCoding\Client\Resolver\Platform\WindowsPhoneMatcher;
use DevCoding\Client\Resolver\Platform\WinRtMatcher;
use DevCoding\Helper\Resolver\CookieBag;
use DevCoding\Hints\Base\BooleanValueInterface;
use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;
use DevCoding\Hints\Base\CookieHintInterface;
use DevCoding\Hints\Base\CookieHintTrait;
use DevCoding\Helper\Resolver\HeaderBag;

/**
 * Returns the value for the Sec-CH-UA-Mobile client hint header, or polyfills the same.  This is intended to be an
 * indicator of whether the device is a small mobile device, such as a phone.
 *
 * References:
 *   https://wicg.github.io/ua-client-hints/#sec-ch-ua-mobile
 *   https://web.dev/user-agent-client-hints/
 *   https://github.com/WICG/ua-client-hints/blob/main/README.md#how-does-sec-ch-ua-mobile-define-mobile
 *
 * Class Mobile
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class Mobile extends Hint implements ConstantAwareInterface, CookieHintInterface
{
  use CookieHintTrait;

  const HEADER  = 'Sec-CH-UA-Mobile';
  const COOKIE  = 'mob';
  const DEFAULT = false;

  public function header(HeaderBag $HeaderBag, $additional = [])
  {
    $value = parent::header($HeaderBag, $additional);
    if (isset($value))
    {
      return $value;
    }

    $bool = $this->isMobilePlatform($HeaderBag) ?? $this->isMobileUserAgent($HeaderBag);
    if (isset($bool))
    {
      return $bool ? BooleanValueInterface::TRUE : BooleanValueInterface::FALSE;
    }

    if ($width = (new Width())->header($HeaderBag))
    {
      return Width::isMobile($width) ? BooleanValueInterface::TRUE : BooleanValueInterface::FALSE;
    }

    if ($ect = (new ECT())->header($HeaderBag))
    {
      return ECT::isSlow($ect) ? BooleanValueInterface::TRUE : BooleanValueInterface::FALSE;
    }

    return null;
  }

  public function cookie(CookieBag $CookieBag)
  {
    if ($width = (new Width())->cookie($CookieBag))
    {
      return Width::isMobile($width) ? BooleanValueInterface::TRUE : BooleanValueInterface::FALSE;
    }

    if ($ect = (new ECT())->cookie($CookieBag))
    {
      return ECT::isSlow($ect) ? BooleanValueInterface::TRUE : BooleanValueInterface::FALSE;
    }

    return null;
  }

  /**
   * Evaluates whether the UA-Platform header hint indicates that we are using a typically "mobile" platform.
   *
   * @return bool|null
   */
  protected function isMobilePlatform(HeaderBag $HeaderBag)
  {
    $platform = $HeaderBag->resolve(Platform::HEADER);

    return 'iOS' == $platform || 'Android' == $platform ? true : null;
  }

  /**
   * Evaluates whether the legacy User Agent string indicates that we are using a typically "mobile" platform.
   *
   * @return bool|null
   */
  protected function isMobileUserAgent(HeaderBag $HeaderBag)
  {
    if ($string = (new LegacyUserAgent())->header($HeaderBag))
    {
      $patterns = [
          IosMatcher::PATTERN,
          AndroidMatcher::PATTERN,
          WinRtMatcher::PATTERN,
          WindowsPhoneMatcher::PATTERN,
          WindowsMobileMatcher::PATTERN
      ];

      foreach ($patterns as $pattern)
      {
        if (preg_match($pattern, $string))
        {
          return true;
        }
      }
    }

    return null;
  }
}
