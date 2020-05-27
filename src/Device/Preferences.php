<?php

namespace DevCoding\Device;

/**
 * Object representing preferences hinted by a device.
 *
 * Class Preferences
 *
 * @see     https://github.com/jonesiscoding/device
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 * @package DevCoding\Device
 */
class Preferences extends HintResolver
{
  /**
   * The user has indicated that they prefer dark mode through a preference on their device.
   *
   * @return bool
   */
  public function isDarkMode()
  {
    return $this->getHint(Hints::KEY_DARK_MODE) ?: false;
  }

  /**
   * The user has indicated that they prefer reduced motion through a preference on their device or browser.
   *
   * @return bool
   */
  public function isReducedMotion()
  {
    return $this->getHint(Hints::KEY_REDUCED_MOTION) ?: false;
  }

  /**
   * Opinionated check to determine if high resolution responsive images should be served to this device. The device
   * must not prefer to save data, must have HTMLImageElement.srcset support, and must have a DPR of > 1.
   *
   * @return bool
   */
  public function isHighRes()
  {
    $dpr = $this->getHeader(Hints::HEADER_DPR) ?: $this->getHint(Hints::KEY_DPR);
    $set = $this->getHint(Hints::KEY_SRCSET);

    return  $dpr > 1 && $set && !$this->isSaveData();
  }

  /**
   * Opinionated check that Returns TRUE if the client is emitting a truthy official Save-Data header, a legacy header
   * indicating a mobile connection, an ECT header indicating a 2G / 3G connection, or cookie provided hint of same.
   *
   * @return bool
   */
  public function isSaveData()
  {
    $header = $this->getHeader(Hints::HEADER_SAVE_DATA);

    if (!is_null($header))
    {
      return $header;
    }
    elseif (in_array($this->getHeader(Hints::HEADER_ECT), ['slow-2g', '2g', '3g']))
    {
      return true;
    }
    else
    {
      if (!is_null($hint = $this->getHint(Hints::KEY_SAVE_DATA)))
      {
        return $hint;
      }
    }

    return false;
  }
}
