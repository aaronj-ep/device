<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Client\Resolver\Platform\AndroidMatcher;
use DevCoding\Client\Resolver\Platform\IosMatcher;
use DevCoding\Client\Resolver\Platform\WindowsMobileMatcher;
use DevCoding\Client\Resolver\Platform\WindowsPhoneMatcher;
use DevCoding\Client\Resolver\Platform\WinRtMatcher;
use DevCoding\Helper\Dependency\CookieBagAwareInterface;
use DevCoding\Helper\Dependency\CookieBagTrait;
use DevCoding\Helper\Dependency\PlatformResolverAwareInterface;
use DevCoding\Hints\Base\HeaderBagHint;
use DevCoding\Helper\Dependency\PlatformResolverTrait;
use DevCoding\Hints\Base\UserAgentTrait;
use DevCoding\Helper\Resolver\HeaderBag;;
use DevCoding\Client\Object\Hardware\Pointer as PointerObject;

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
class Mobile extends HeaderBagHint implements CookieBagAwareInterface, PlatformResolverAwareInterface
{
  use CookieBagTrait;
  use UserAgentTrait;
  use PlatformResolverTrait;

  const KEY = 'Sec-CH-UA-Mobile';

  public function get()
  {
    return $this->header(self::KEY) ??
           $this->cookie('h.pc') ??
           $this->isMobilePlatform() ??
           $this->isMobileUserAgent() ??
           $this->isMobileHardware() ??
           $this->getDefault()
    ;
  }

  public function getDefault()
  {
    return false;
  }

  public function isNative()
  {
    return true;
  }

  public function isVendor()
  {
    return false;
  }

  public function isDraft()
  {
    return false;
  }

  public function isStatic()
  {
    return true;
  }

  /**
   * @return bool
   */
  protected function isCoarse()
  {
    return PointerObject::COARSE == $this->getPointer();
  }

  /**
   * Evaluates whether the UA-Platform header hint indicates that we are using a typically "mobile" platform.
   *
   * @return bool|null
   */
  protected function isMobilePlatform()
  {
    return $this->isPlatformHint('iOS') || $this->isPlatformHint('Android') ? true : null;
  }

  protected function isDesktopPlatform()
  {

  }

  /**
   * Evaluates whether the legacy User Agent string indicates that we are using a typically "mobile" platform.
   *
   * @return bool|null
   */
  protected function isMobileUserAgent()
  {
    if ($UA = $this->getUserAgentObject())
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
        if ($UA->isMatch($pattern))
        {
          return true;
        }
      }
    }

    return null;
  }

  /**
   * Evaluates whether the device is likely to be mobile based on the ECT or pointer and viewport width.
   *
   * @return bool|null
   */
  protected function isMobileHardware()
  {
    if ($this->getViewPortWidth() <= 480)
    {
      if (PointerObject::COARSE == $this->getPointer())
      {
        return true;
      }
      elseif ($ect = $this->getEct())
      {
        return '3g' === $ect || '2g' === $ect || 'slow-2g' === $ect;
      }
    }

    return null;
  }

  /**
   * @return HeaderBag
   */
  protected function getHeaderBag()
  {
    return $this->_HeaderBag;
  }

  /**
   * @return string|null
   */
  private function getEct()
  {
    // We don't want to use the hint, because we only want the header & cookie used, no extrapolation.
    return $this->header(ECT::KEY) ?? $this->cookie(ECT::COOKIE);
  }

  /**
   * @return string
   */
  private function getPointer()
  {
    return (new Pointer())->setHeaderBag($this->_HeaderBag)->setCookieBag($this->_CookieBag)->get();
  }

  /**
   * @return string
   */
  private function getViewPortWidth()
  {
    return (new ViewportHeight())->setHeaderBag($this->_HeaderBag)->setCookieBag($this->_CookieBag)->get();
  }
}
