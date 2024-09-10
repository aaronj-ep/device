<?php

namespace DevCoding\Device;

use DevCoding\Hints\Hint\ColorScheme;
use DevCoding\Hints\Hint\Contrast;
use DevCoding\Hints\Hint\DPR;
use DevCoding\Hints\Hint\ReducedData;
use DevCoding\Hints\Hint\ReducedMotion;
use DevCoding\Hints\Hint\ReducedTransparency;
use DevCoding\Hints\Hint\SaveData;

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
class Preferences extends DeviceChild
{
  public function getColorScheme()
  {
    return $this->ClientHints->get(ColorScheme::HEADER);
  }

  public function getContrast()
  {
    return $this->ClientHints->get(Contrast::HEADER);
  }

  /**
   * The user has indicated that they prefer dark mode through a preference on their device.
   *
   * @deprecated
   * @return bool
   */
  public function isDarkMode()
  {
    return ColorScheme::DARK == $this->getColorScheme();
  }

  /**
   * @return bool
   */
  public function isIncreasedContrast()
  {
    return Contrast::MORE == $this->getContrast();
  }

  /**
   * @return bool
   */
  public function isReducedContrast()
  {
    return Contrast::LESS == $this->getContrast();
  }

  /**
   * @return bool
   */
  public function isReducedData(): bool
  {
    return $this->ClientHints->bool(ReducedData::HEADER, false);
  }

  /**
   * The user has indicated that they prefer reduced motion through a preference on their device or browser.
   *
   * @return bool
   */
  public function isReducedMotion(): bool
  {
    return $this->ClientHints->bool(ReducedMotion::HEADER, false);
  }

  /**
   * @return bool
   */
  public function isReducedTransparency(): bool
  {
    return $this->ClientHints->bool(ReducedTransparency::HEADER, false);
  }

  /**
   * Opinionated check to determine if high resolution responsive images should be served to this device. The device
   * must not prefer to save data, must have HTMLImageElement.srcset support, and must have a DPR of > 1.
   *
   * @return bool
   */
  public function isHighRes()
  {
    $dpr = $this->ClientHints->get(DPR::HEADER);
    $set = $this->ClientHints->bool('HTML_IMG_SRCSET');

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
    return $this->ClientHints->get(SaveData::HEADER);
  }
}
