<?php

namespace DevCoding\Device;

use DevCoding\Helper\Dependency\ClientHintsAwareInterface;
use DevCoding\Helper\Dependency\ClientHintsTrait;
use DevCoding\Helper\Dependency\FeatureHintsAwareInterface;
use DevCoding\Helper\Dependency\FeatureHintsTrait;
use DevCoding\Hints\ClientHints;
use DevCoding\Hints\ColorScheme;
use DevCoding\Hints\Contrast;
use DevCoding\Hints\FeatureHints;

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
class Preferences implements ClientHintsAwareInterface, FeatureHintsAwareInterface
{
  use ClientHintsTrait;
  use FeatureHintsTrait;

  public function getColorScheme()
  {
    return $this->getClientHints()->get(ClientHints::COLOR_SCHEME);
  }

  public function getContrast()
  {
    return $this->getClientHints()->get(ClientHints::CONTRAST);
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
  public function isReducedData()
  {
    return $this->getClientHints()->get(ClientHints::REDUCED_DATA);
  }

  /**
   * The user has indicated that they prefer reduced motion through a preference on their device or browser.
   *
   * @return bool
   */
  public function isReducedMotion()
  {
    return $this->getClientHints()->get(ClientHints::REDUCED_MOTION);
  }

  /**
   * @return bool
   */
  public function isReducedTransparency()
  {
    return $this->getClientHints()->get(ClientHints::REDUCED_TRANSPARENCY);
  }

  /**
   * Opinionated check to determine if high resolution responsive images should be served to this device. The device
   * must not prefer to save data, must have HTMLImageElement.srcset support, and must have a DPR of > 1.
   *
   * @return bool
   */
  public function isHighRes()
  {
    $dpr = $this->getClientHints()->get(ClientHints::DPR);
    $set = $this->getFeatureHints()->isSupported(FeatureHints::HTML_SRCSET);

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
    return $this->getClientHints()->get(ClientHints::SAVE_DATA);
  }
}
