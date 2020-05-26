<?php

namespace DevCoding\Device;

class Features extends HintResolver
{
  /**
   * Given an array of feature keys, returns only the keys that this the represented device does not have.
   * This can be used to polyfill the missing features if possible.
   *
   * @param array $featureKeys
   *
   * @return array
   */
  public function getMissing($featureKeys)
  {
    $retval = [];
    foreach ($featureKeys as $key)
    {
      if (!$this->isSupported($key))
      {
        $retval[] = $key;
      }
    }

    return $retval;
  }

  /**
   * Determines if the feature is supported based on the injected client hints.
   *
   * @param string|array $key_or_keys
   *
   * @return bool
   */
  public function isSupported($key_or_keys)
  {
    if (is_array($key_or_keys))
    {
      foreach ($key_or_keys as $key)
      {
        if (!$this->isSupported($key))
        {
          return false;
        }
      }

      return true;
    }
    else
    {
      return $this->getHint($key_or_keys) ?: false;
    }
  }

  // region //////////////////////////////////////////////// CSS Features

  /**
   * TRUE if the device has hinted that it has the CSS "display: flex" property.
   *
   * @return bool
   */
  public function isDisplayFlex()
  {
    return $this->getHint(Hints::KEY_DISPLAY_FLEX) ?: false;
  }

  /**
   * TRUE if the device has hinted that it has the CSS "display: grid" property.
   *
   * @return bool
   */
  public function isDisplayGrid()
  {
    return $this->getHint(Hints::KEY_DISPLAY_GRID) ?: false;
  }

  // endregion ///////////////////////////////////////////// End CSS Features

  // region //////////////////////////////////////////////// Hardware Features

  public function isLegacyTouch()
  {
    return $this->getHint(Hints::KEY_TOUCH_LEGACY) ?: false;
  }

  /**
   * @return bool
   */
  public function isPointerCoarse()
  {
    return $this->getHint(Hints::KEY_TOUCH) ?: false;
  }

  // endregion ///////////////////////////////////////////// End Hardware Features

  // region //////////////////////////////////////////////// HTMLElement Features

  /**
   * @return bool
   */
  public function isLazyLoading()
  {
    return $this->getHint(Hints::KEY_LOADING) ?: false;
  }

  /**
   * @return bool
   */
  public function isSrcSet()
  {
    return $this->getHint(Hints::KEY_SRCSET) ?: false;
  }

  // endregion ///////////////////////////////////////////// End HTMLElementFeatures

  // region //////////////////////////////////////////////// Javascript Features

  public function isArrayIncludes()
  {
    return $this->getHint(Hints::KEY_ARRAY_INCLUDES) ?: false;
  }

  public function isPromise()
  {
    return $this->getHint(Hints::KEY_PROMISE) ?: false;
  }

  // endregion ///////////////////////////////////////////// End Javascript Features
}
