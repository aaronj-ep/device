<?php
/**
 * Pointer.php
 */

namespace DevCoding\Hints\Hint;

use DevCoding\Client\Object\Platform\PlatformInterface;
use DevCoding\Client\Resolver\Platform\IosMatcher;
use DevCoding\Client\Resolver\Platform\TvOsMatcher;
use DevCoding\Helper\Dependency\CookieBagAwareInterface;
use DevCoding\Helper\Dependency\CookieBagTrait;
use DevCoding\Helper\Dependency\PlatformResolverAwareInterface;
use DevCoding\Hints\Base\HeaderBagHint;
use DevCoding\Helper\Dependency\PlatformResolverTrait;
use DevCoding\Hints\Base\UserAgentTrait;
use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Client\Object\Hardware\Pointer as PointerObject;

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
class Pointer extends HeaderBagHint implements CookieBagAwareInterface, PlatformResolverAwareInterface
{
  use CookieBagTrait;
  use UserAgentTrait;
  use PlatformResolverTrait;

  const KEY = 'CH-Pointer';

  /** @var PointerObject */
  protected $_pointer;

  /**
   * @return string
   */
  public function get()
  {
    return $this->getObject()->getType();
  }

  /**
   * @return string
   */
  public function getDefault()
  {
    return PointerObject::FINE;
  }

  /**
   * @return bool
   */
  public function isNative()
  {
    return false;
  }

  /**
   * @return bool
   */
  public function isVendor()
  {
    return false;
  }

  /**
   * @return bool
   */
  public function isDraft()
  {
    return false;
  }

  /**
   * @return bool
   */
  public function isStatic()
  {
    return false;
  }

  /**
   * @return PointerObject
   */
  public function getObject()
  {
    if (!isset($this->_pointer))
    {
      if ($this->cookie('h.pc'))
      {
        // Cookie specifies, no more mystery...
        $this->_pointer = new PointerObject(PointerObject::COARSE, true);
      }
      else
      {
        // Otherwise we'll have to do some detective work...
        $this->_pointer = new PointerObject(PointerObject::INCONCLUSIVE);

        if ($this->getUserAgentObject()->isMatch(IosMatcher::PATTERN, null, $iMatch))
        {
          // These iDevices have a touch screen.
          $isTouch = in_array($iMatch[1], ['iPod touch', 'iPhone', 'iPad']);
          // For all versions of iOS (currently <=15), all devices would have a coarse pointer, even if they have a keyboard attached.
          $this->_pointer = new PointerObject(PointerObject::COARSE, $isTouch);
        }
        elseif ($this->getUserAgentObject()->isMatch(TvOsMatcher::PATTERN))
        {
          // TV, ChromeCast, Streaming, and Set-Top Gaming Devices aren't going to have fine pointer control.
          // But you wouldn't consider them to have a touch screen.
          $this->_pointer = new PointerObject(PointerObject::COARSE, false);
        }
        elseif ($Platform = $this->getPlatformObject())
        {
          if ($name = $Platform->getPlatform())
          {
            if (PlatformInterface::MACOS === $name)
            {
              // We always assume 'fine' on macOS, as there are no touchscreen Macs through macOS 16
              $this->_pointer = new PointerObject(PointerObject::FINE, false);
            }
            elseif (in_array($name, [PlatformInterface::ANDROID, PlatformInterface::WINMOBILE, PlatformInterface::WINPHONE, PlatformInterface::WINRT]))
            {
              // Windows Mobile 5.0 Smartphone and Windows Mobile 6.x Standard didn't have touch.
              // Other Windows Mobile, Windows Phone, Android, and Windows RT all have touch.
              $isTouch = PlatformInterface::WINMOBILE !== $name || !$this->getUserAgentObject()->isMatch('#(Standard|Smartphone)#', false);

              // We always assume 'coarse' on these mobile platforms.
              $this->_pointer = new PointerObject(PointerObject::COARSE, $isTouch);
            }
            elseif (PlatformInterface::WINNT === $name)
            {
              if ($Version = $Platform->getVersion())
              {
                if (7 <= $Version->getMajor())
                {
                  // We can be pretty sure that anything before Windows 7 didn't have a touch screen
                  // that functions similarly enough to a modern touchscreen to warrant hinting such.
                  // We're also going to assume that there is a mouse present. This is a bad assumption,
                  // but still likely to be the right choice as the devices weren't designed correctly for
                  // screen only use.
                  $this->_pointer = new PointerObject(PointerObject::FINE, false);
                }
                else
                {
                  // This isn't really conclusive, but there are very few false positives.
                  $isTouch = $this->getUserAgentObject()->isMatch('#(Touch|touch|Tablet)#', false);
                  // Since we don't know if there's a mouse present....
                  $this->_pointer = new PointerObject(PointerObject::INCONCLUSIVE, $isTouch);
                }
              }
            }
          }
        }
      }
    }

    return $this->_pointer;
  }

  /**
   * @return HeaderBag
   */
  protected function getHeaderBag()
  {
    return $this->_HeaderBag;
  }
}

