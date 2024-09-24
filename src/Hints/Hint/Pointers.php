<?php
/**
 * Pointer.php
 */

namespace DevCoding\Hints\Hint;

use DevCoding\Client\Object\Headers\UserAgentString;
use DevCoding\Client\Object\Platform\PlatformInterface;
use DevCoding\Client\Object\Version\ClientVersion;
use DevCoding\Client\Resolver\Platform\AndroidMatcher;
use DevCoding\Client\Resolver\Platform\ChromeOsMatcher;
use DevCoding\Client\Resolver\Platform\IosMatcher;
use DevCoding\Client\Resolver\Platform\LinuxMatcher;
use DevCoding\Client\Resolver\Platform\MacOsMatcher;
use DevCoding\Client\Resolver\Platform\TvOsMatcher;
use DevCoding\Client\Resolver\Platform\WindowsMobileMatcher;
use DevCoding\Client\Resolver\Platform\WindowsPhoneMatcher;
use DevCoding\Client\Resolver\Platform\WinNtMatcher;
use DevCoding\Client\Resolver\Platform\WinRtMatcher;
use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;
use DevCoding\Hints\Base\CookieHintInterface;
use DevCoding\Hints\Base\CookieHintTrait;
use DevCoding\Client\Object\Hardware\Pointers as PointersObject;
use DevCoding\Hints\Base\ListValueInterface;

/**
 * Returns an indication of the type of pointer that a device is using, either 'coarse', 'fine', or 'inconclusive'.
 * This hint is not part of an official specification or draft proposal at this time.
 *
 * Class Pointer
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class Pointers extends Hint implements ConstantAwareInterface, ListValueInterface, CookieHintInterface
{
  use CookieHintTrait;

  const HEADER  = 'CH-Pointers';
  const COOKIE  = 'p';
  const DEFAULT = 'coarse';
  const DRAFT   = false;
  const STATIC  = false;
  const VENDOR  = true;

  public function header(HeaderBag $HeaderBag, $additional = [])
  {
    $value = parent::header($HeaderBag);
    if (isset($value))
    {
      return $value;
    }

    $object = $this->getObjectFromFormFactors($HeaderBag) ?? $this->getObjectFromPlatform($HeaderBag) ?? $this->getObjectFromUserAgentString($HeaderBag) ?? null
    ;

    return $object ? (string) $object : null;
  }

  public function default()
  {
    return implode(', ', [PointersObject::COARSE, PointersObject::FINE]);
  }


  /**
   * Attempts to determine the available pointers from the 'Sec-CH-UA-Form-Factors' header
   *
   * @return PointersObject|null
   */
  protected function getObjectFromFormFactors(HeaderBag $HeaderBag)
  {
    if ($header = $HeaderBag->resolve('Sec-CH-UA-Form-Factors'))
    {
      $fine     = '#(desktop)#i';
      $primary  = PointersObject::NONE;
      $pointers = [];

      if (!preg_match($fine, $header))
      {
        $pointers[] = $primary = PointersObject::COARSE;
      }
      else
      {
        // We know we have a fine pointer included
        $pointers[] = PointersObject::FINE;
        if (!in_array(PointersObject::COARSE, $pointers))
        {
          // If we do not also have a coarse pointer, then set the primary to FINE.
          $primary = PointersObject::FINE;
        }
      }

      return new PointersObject($primary, $pointers);
    }

    return null;
  }

  /**
   * Attempt to determine the available pointers from the 'Sec-CH-UA-Platform' header.
   *
   * @return PointersObject|null
   */
  protected function getObjectFromPlatform(HeaderBag $HeaderBag)
  {
    if ($header = $HeaderBag->resolve(Platform::HEADER))
    {
      $pointers = [];
      if (PlatformInterface::WINNT === $header)
      {
        // Assuming Hybrid, as touch-only devices are less common with Windows
        $pointers[] = $primary = PointersObject::COARSE;
        $pointers[] = PointersObject::FINE;
      }
      elseif (PlatformInterface::IOS === $header)
      {
        $pointers[] = $primary = PointersObject::COARSE;
        if ($header = $HeaderBag->resolve(Model::HEADER))
        {
          $isPodAny   = false !== strpos($header, 'Pod');
          $isPodTouch = $isPodAny && strpos($header, 'touch');
          if ($isPodAny && !$isPodTouch)
          {
            // While these headers being available with a non-touch iPod is unlikely, the header could be polyfilled,
            // so we'll play along and add that it's touchless.
            $pointers[] = PointersObject::TOUCHLESS;
          }
        }
      }
      elseif (PlatformInterface::ANDROID === $header)
      {
        // While we could have a fine pointing device in Android, it's less common
        $pointers[] = $primary = PointersObject::COARSE;
      }
      elseif (preg_match(TvOsMatcher::PATTERN, $header))
      {
        // Most TV/Game devices do not have a fine pointing device OR touch
        $pointers[] = $primary = PointersObject::COARSE;
        $pointers[] = PointersObject::TOUCHLESS;
      }
      elseif (PlatformInterface::MACOS === $header)
      {
        // No touchscreens on macOS as of macOS 14
        $pointers[] = $primary = PointersObject::FINE;
      }
      elseif (PlatformInterface::CHROMEOS === $header || PlatformInterface::LINUX === $header)
      {
        // ChromeOS devices have been made in Coarse-only, Fine-only AND Hybrid form factors.
        // Linux devices could be nearly anything
        // Safest to go with hybrid here
        $pointers[] = $primary = PointersObject::COARSE;
        $pointers[] = PointersObject::FINE;
      }
      else
      {
        // WinRT, Windows Phone, Windows Mobile are unlikely to give this header
        // Pointers on other devices are better identified another way.
        return null;
      }

      return new PointersObject($primary, $pointers);
    }

    return null;
  }

  /**
   * Attempt to determine the available pointers from the legacy user agent string.
   *
   * @return PointersObject|null
   */
  protected function getObjectFromUserAgentString(HeaderBag $HeaderBag)
  {
    $pointers = [];
    if ($UserAgentObj = $this->getUserAgentObject($HeaderBag))
    {
      if ($UserAgentObj->isMatch(WinNtMatcher::PATTERN))
      {
        $version = $this->getPlatformVersion($HeaderBag);
        $version = $version ? WinNtMatcher::normalizeHint($version) : null;
        if ($version && 7 >= $version)
        {
          // We can be pretty sure that anything before Windows 7 didn't have a touch screen
          // that functions similarly enough to a modern touchscreen to warrant hinting such.
          // We're also going to assume that there is a mouse present. This is a bad assumption,
          // but still likely to be the right choice as the devices weren't designed correctly for
          // screen only use.
          $pointers[] = $primary = PointersObject::FINE;
        }
        else
        {
          // As touch-only devices are less common w/ Windows, we assume hybrid
          $pointers[] = $primary = PointersObject::COARSE;
          $pointers[] = PointersObject::FINE;
        }
      }
      elseif ($UserAgentObj->isMatch(IosMatcher::PATTERN, null, $iMatch))
      {
        $pointers[] = $primary = PointersObject::COARSE;

        // If not these, probably no touchscreen
        if (!preg_match('#(iPad|iPhone|touch|Vision)#i', $iMatch[1]))
        {
          $pointers[] = PointersObject::TOUCHLESS;
        }

        // We don't have any way to determine if there's a Magic Keyboard or if Universal Control is being used
        // however both of these would be less common situations so we assume not.
      }
      elseif($UserAgentObj->isMatch(MacOsMatcher::PATTERN))
      {
        // No macOS with touch as of macOS 14
        $pointers[] = $primary = PointersObject::FINE;
      }
      elseif ($UserAgentObj->isMatch(AndroidMatcher::PATTERN))
      {
        // While these could have a fine pointing device, it's safer to assume pure touch based on the numbers
        $pointers[] = $primary = PointersObject::COARSE;
      }
      elseif ($UserAgentObj->isMatch(ChromeOsMatcher::PATTERN) || $UserAgentObj->isMatch(LinuxMatcher::PATTERN))
      {
        // A condundrum; we could be pure coarse, hybrid, pure fine here.
        // Safest to go with hybrid with coarse as primary.
        $pointers[] = $primary = PointersObject::COARSE;
        $pointers[] = PointersObject::FINE;
      }
      elseif ($UserAgentObj->isMatch(TvOsMatcher::PATTERN))
      {
        // TV, ChromeCast, Streaming, and Set-Top Gaming Devices aren't going to have fine pointer control.
        // But you wouldn't consider them to have a touch screen.
        $pointers[] = $primary = PointersObject::COARSE;
        $pointers[] = PointersObject::TOUCHLESS;
      }
      elseif ($UserAgentObj->isMatch(WinRtMatcher::PATTERN))
      {
        // These might have a fine pointer via the keyboard attachment, but they might not.
        // They always have a touchscreen, though.
        $pointers[] = $primary = PointersObject::COARSE;
      }
      elseif ($UserAgentObj->isMatch(WindowsMobileMatcher::PATTERN))
      {
        $pointers[] = $primary = PointersObject::COARSE;
        if ($UserAgentObj->isMatch('#(Standard|Smartphone)#'))
        {
          // Windows Mobile 5.0 Smartphone and Windows Mobile 6.x Standard didn't have touch.
          $pointers[] = PointersObject::TOUCHLESS;
        }
      }
      elseif ($UserAgentObj->isMatch(WindowsPhoneMatcher::PATTERN))
      {
        $pointers[] = $primary = PointersObject::COARSE;
      }
      else
      {
        // Inconclusive
        return null;
      }

      return new PointersObject($primary, $pointers);
    }

    return null;
  }

  /**
   * @param HeaderBag $HeaderBag
   *
   * @return UserAgentString|null
   */
  private function getUserAgentObject(HeaderBag $HeaderBag)
  {
    if ($str = (new LegacyUserAgent())->header($HeaderBag))
    {
      return new UserAgentString($str);
    }

    return null;
  }

  /**
   * @return ClientVersion|null
   */
  private function getPlatformVersion(HeaderBag $HeaderBag)
  {
    return (new PlatformVersion())->header($HeaderBag);
  }
}
