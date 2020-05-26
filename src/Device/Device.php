<?php

namespace DevCoding\Device;

/**
 * Device.
 *
 * Class Device
 *
 * @package DevCoding\Device
 */
class Device extends HintResolver
{
  /** @var Features */
  protected $_Features;
  /** @var Preferences */
  protected $_Preferences;
  /** @var Hardware */
  protected $_Hardware;

  public static function create()
  {
    return new static();
  }

  // region //////////////////////////////////////////////// Public Getters

  /**
   * @return Features
   */
  public function Features()
  {
    return $this->getFeatures();
  }

  /**
   * @return Hardware
   */
  public function Hardware()
  {
    return $this->getHardware();
  }

  /**
   * @return Preferences
   */
  public function Preferences()
  {
    return $this->getPreferences();
  }

  /**
   * Retrieves object offering client hints for Browser Features.  See object for specifics.
   *
   * @return Features
   */
  public function getFeatures()
  {
    if (!$this->_Features instanceof Features)
    {
      $this->_Features = new Features($this->getHints());
    }

    return $this->_Features;
  }

  /**
   * @return Hardware
   */
  public function getHardware()
  {
    if (!$this->_Hardware instanceof Hardware)
    {
      $this->_Hardware = new Hardware($this->getHints());
    }

    return $this->_Hardware;
  }

  /**
   * Retrieves object offering client hints for user preferences.  See object for specifics.
   *
   * @return Preferences
   */
  public function getPreferences()
  {
    if (!$this->_Preferences instanceof Preferences)
    {
      $this->_Preferences = new Preferences($this->getHints());
    }

    return $this->_Preferences;
  }

  /**
   * @return bool
   */
  public function isHinted()
  {
    return $this->getHints()->isHinted();
  }

  // endregion ///////////////////////////////////////////// End Public Getters

  // region //////////////////////////////////////////////// Setters

  /**
   * @param Features $DeviceFeatures
   *
   * @return Device
   */
  public function setFeatures($DeviceFeatures)
  {
    $this->_Features = $DeviceFeatures;

    return $this;
  }

  /**
   * @param Hardware $Hardware
   *
   * @return Device
   */
  public function setHardware($Hardware)
  {
    $this->_Hardware = $Hardware;

    return $this;
  }

  /**
   * @param Preferences $DevicePrefs
   *
   * @return Device
   */
  public function setPreferences($DevicePrefs)
  {
    $this->_Preferences = $DevicePrefs;

    return $this;
  }

  // endregion ///////////////////////////////////////////// End Setters
}
