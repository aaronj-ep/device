<?php

namespace DevCoding\Device;

use DevCoding\ValueObject\Internet\Browser\BrowserAbstract;
use DevCoding\ValueObject\Internet\Browser\Chrome;
use DevCoding\ValueObject\Internet\Browser\Edg;
use DevCoding\ValueObject\Internet\Browser\Edge;
use DevCoding\ValueObject\Internet\Browser\Firefox;
use DevCoding\ValueObject\Internet\Browser\Generic;
use DevCoding\ValueObject\Internet\Browser\InternetExplorer;
use DevCoding\ValueObject\Internet\Browser\Opera;
use DevCoding\ValueObject\Internet\Browser\Safari;
use DevCoding\ValueObject\System\Platform;

/**
 * Provides Client Hints using the User Agent provided by the device.  As user agent strings can be misinterpeted or
 * spoofed, this is not a reliable method of detecting device features and should be used as a backup, preferably only
 * on the first load, before a cookie can be set to provide improved feature hinting to the application.
 *
 * Class HintsWithFallback
 *
 * @see     https://github.com/jonesiscoding/device
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 * @package DevCoding\Device
 */
class HintsWithFallback extends Hints
{
  const REGEX_IDEVICE    = '#(iPod touch|iPod|iPad|iPhone).+Version#';
  const REGEX_TELEVISION = "#CrKey|Apple TV|tvOS|AFTT|AFTM|Roku|SmartTV|SMART\-TV|\.TV|DTV|HbbTV|XBox#";

  /** @var BrowserAbstract Determined from the user agent. */
  protected $_browser;
  /** @var Platform Determined from the user agent. */
  protected $_Platform;

  protected $browsers = [Chrome::class,
                         Edg::class,
                         Firefox::class,
                         Edge::class,
                         InternetExplorer::class,
                         Safari::class,
                         Opera::class,
                         Generic::class,
  ];

  protected $features = [
      self::KEY_ARRAY_INCLUDES,
      self::KEY_PROMISE,
      self::KEY_DISPLAY_GRID,
      self::KEY_DISPLAY_FLEX,
      self::KEY_LOADING,
      self::KEY_SRCSET,
  ];

  /**
   * Override to populate the hints array based on the user agent, only if the hint cookie is not available.
   *
   * @param string $key
   *
   * @return array|bool|mixed|null
   */
  public function get($key)
  {
    if (is_null($this->_decode) && !$this->isHinted() && !$this->isHeadless())
    {
      $this->setHintsByUserAgent();
    }

    return parent::get($key);
  }

  /**
   * @return BrowserAbstract|null
   */
  public function getBrowser()
  {
    if ($UserAgent = $this->getUserAgent())
    {
      if (!$this->_browser instanceof BrowserAbstract)
      {
        foreach ($this->browsers as $brClass)
        {
          if (!$this->_browser instanceof BrowserAbstract)
          {
            if (class_exists($brClass) && method_exists($brClass, 'fromUserAgent'))
            {
              if ($Browser = $brClass::fromUserAgent($UserAgent))
              {
                $this->_browser = $Browser;
              }
            }
          }
        }
      }

      return $this->_browser;
    }

    return null;
  }

  /**
   * @return Platform|null
   */
  public function getPlatform()
  {
    if ($UserAgent = $this->getUserAgent())
    {
      if (!$this->_Platform instanceof Platform)
      {
        if ($Platform = Platform::fromUserAgent($UserAgent))
        {
          $this->_Platform = $Platform;
        }
      }
    }

    return $this->_Platform;
  }

  /**
   * @return bool
   */
  public function isBot()
  {
    return ($UA = $this->getUserAgent()) ? $UA->isBot() : false;
  }

  public function isPlatform($key)
  {
    return ($Platform = $this->getPlatform()) && $key === $Platform->getName();
  }

  // region //////////////////////////////////////////////// Hint Setter Methods

  /**
   * @param BrowserAbstract $Browser
   *
   * @return HintsWithFallback
   */
  protected function setHintsByBrowser(BrowserAbstract $Browser)
  {
    foreach ($this->features as $feature)
    {
      $this->setHintByKey($feature, $Browser->isSupported($feature) ?: null);
    }

    $this->_browser = $Browser;

    return $this->setHintByKey(self::KEY_MOBILE, $Browser->isMobile() ?: null);
  }

  protected function setHintsByPlatform(Platform $Platform)
  {
    if (in_array($Platform->getName(), [Platform::MACOS, Platform::TVOS]))
    {
      // MacOS, tvOS do not support touch
      $this->setHintByKey(self::KEY_TOUCH_LEGACY, false);
      $this->setHintByKey(self::KEY_TOUCH, false);
    }

    if (Platform::WIN_NT === $Platform->getName())
    {
      if ($Platform->getMajor() < 7)
      {
        // We can be pretty sure that anything before Windows 7 didn't have a touch screen
        // that functions similarly enough to a modern touchscreen to warrant hinting such.
        $this->setHintByKey(self::KEY_TOUCH_LEGACY, false);
        $this->setHintByKey(self::KEY_TOUCH, false);
      }
    }

    if (in_array($Platform->getName(), [Platform::WIN_RT, Platform::WIN_MOBILE, Platform::WIN_PHONE]))
    {
      // We can assume that touch is available here, but we'll leave the pointer:coarse check up
      // to the browser, as they could be using a browser other than the default on Mobile/Phone.
      if (Platform::WIN_RT === $Platform->getName())
      {
        $this->setHintByKey(self::KEY_TOUCH_LEGACY, true);

        if ($Platform->getMinor() < 1)
        {
          // This is almost certainly a SurfaceRT.  The Surface 2 came with Win8.1
          $this->setHintByKey(self::KEY_WIDTH, 1366);
          $this->setHintByKey(self::KEY_HEIGHT, 768);
          $this->setHintByKey(self::KEY_DPR, 1);
        }
      }
    }
  }

  /**
   * Sets the browser, hardware, and other hints based on the user agent.
   *
   * @return $this
   */
  protected function setHintsByUserAgent()
  {
    if ($Browser = $this->getBrowser())
    {
      $this->setHintsByBrowser($Browser);
    }

    if ($Platform = $this->getPlatform())
    {
      $this->setHintsByPlatform($Platform);
    }

    // Hardware Specific Hints
    $iMatch = [];
    if ($UAgent = $this->getUserAgent())
    {
      if ($UAgent->isMatch(self::REGEX_IDEVICE, false, $iMatch))
      {
        // These iDevices all have touch screens in all instances
        $isTouch = in_array($iMatch[1], ['iPod touch', 'iPhone', 'iPad']);
        $this->setHintByKey(self::KEY_TOUCH_LEGACY, $isTouch);

        // iOS Safari Support Interaction Media Features as of iOS v9
        $isMediaInteraction = $isTouch ? $Browser->isVersionUp(9) : null;
        $this->setHintByKey(self::KEY_TOUCH, $isMediaInteraction);

        // And obviously we know a mobile device
        $this->setHintByKey(self::KEY_MOBILE, true);
      }
      // TV, ChromeCast, Streaming, and Set-Top Gaming Devices aren't going to be touch OR mobile
      elseif ($UAgent->isMatch(self::REGEX_TELEVISION))
      {
        $this->setHintByKey(self::KEY_TOUCH, false);
        $this->setHintByKey(self::KEY_TOUCH_LEGACY, false);
        $this->setHintByKey(self::KEY_MOBILE, false);
      }
      else
      {
        // Pretty much a dead giveaway, but we put it here to make sure a TV isn't marked as touch, even if it is.
        $this->setHintByKey(self::KEY_TOUCH_LEGACY, $UAgent->isMatch('#(touch)#i') ?: null);
      }
    }

    return $this;
  }

  // endregion ///////////////////////////////////////////// End Hint Setter Methods
}
