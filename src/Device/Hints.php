<?php

namespace DevCoding\Device;

use DevCoding\ValueObject\Internet\UserAgent;

/**
 * Provides client hints by interpeting various server headers and a cookie set by the device.js javascript.
 *
 * References:
 *   * https://developers.google.com/web/updates/2015/09/automating-resource-selection-with-client-hints
 *   * http://httpwg.org/http-extensions/client-hints.html
 *   * https://developers.google.com/web/updates/2016/02/save-data
 *
 * Class DeviceHints
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 */
class Hints
{
  // Keys for hints.  Key dot structure indicates source and type of hint.
  const KEY_ARRAY_INCLUDES  = 'feature.jsArrayIncludes';
  const KEY_DARK_MODE       = 'pref.darkMode';
  const KEY_DISPLAY_FLEX    = 'feature.cssDisplayFlex';
  const KEY_DISPLAY_GRID    = 'feature.cssDisplayGrid';
  const KEY_DPR             = 'hardware.devicePixelRatio';
  const KEY_ECT             = 'hardware.effectiveConnectionType';
  const KEY_HEIGHT          = 'hardware.deviceHeight';
  const KEY_LOADING         = 'feature.imgLoading';
  const KEY_METRO           = 'pref.metroMode';
  const KEY_MOBILE          = 'mobile';
  const KEY_REDUCED_MOTION  = 'pref.reducedMotion';
  const KEY_PROMISE         = 'feature.jsPromise';
  const KEY_SAVE_DATA       = 'pref.saveData';
  const KEY_SRCSET          = 'feature.imgSrcSet';
  const KEY_TOUCH           = 'hardware.pointerCoarse';
  const KEY_TOUCH_LEGACY    = 'hardware.touch';
  const KEY_VIEWPORT_HEIGHT = 'hardware.viewportHeight';
  const KEY_VIEWPORT_WIDTH  = 'hardware.viewportWidth';
  const KEY_WIDTH           = 'hardware.deviceWidth';

  // Client Hint Headers
  const HEADER_DPR            = 'dpr';
  const HEADER_WIDTH          = 'width';
  const HEADER_ECT            = 'ect';
  const HEADER_SAVE_DATA      = 'Save-Data';
  const HEADER_VIEWPORT_WIDTH = 'viewport_width';

  // Opinionated list of headers that imply that data should be saved
  const HEADERS_SAVE_DATA = [
      'HTTP_SAVE_DATA',
      'HTTP_X_WAP_PROFILE',
      'HTTP_X_WAP_PROFILE',
      'HTTP_ATT_DEVICEID',
      'HTTP_WAP_CONNECTION',
      'HTTP_X_ROAMING',
      'HTTP_X_MOBILE_UA',
      'HTTP_X_MOBILE_GATEWAY',
  ];

  // List of possible headers that could provide a user agent
  const HEADERS_USER_AGENT = [
      'HTTP_USER_AGENT',
      'HTTP_X_OPERAMINI_PHONE_UA',
      'HTTP_X_DEVICE_USER_AGENT',
      'HTTP_X_ORIGINAL_USER_AGENT',
      'HTTP_X_SKYFIRE_PHONE',
      'HTTP_X_BOLT_PHONE_UA',
      'HTTP_DEVICE_STOCK_UA',
      'HTTP_X_UCBROWSER_DEVICE_UA',
  ];

  /** @var string The cookie name set by device.js */
  protected $_cookie = 'djs';

  // Value Storage

  /** @var array|null Cached hint values after being decoded from the cookie */
  protected $_decode = null;
  /** @var UserAgent|null Cached user agent object */
  protected $_ua;
  /** @var bool|null Cached value determined from headers by getSaveData method. */
  protected $_saveData;

  /**
   * @param string $cookie
   */
  public function __construct(string $cookie = null)
  {
    if ($cookie)
    {
      $this->_cookie = $cookie;
    }
  }

  // region //////////////////////////////////////////////// General Getters/**

  /**
   * Returns the given hint value from the array of hints saved, or return NULL if the hint is not found.
   *
   * @param string $key
   *
   * @return bool|mixed|null
   */
  public function get($key)
  {
    if (is_null($this->_decode))
    {
      $this->_decode = $this->isHinted() ? $this->decodeHints($_COOKIE[$this->_cookie]) : [];
    }

    $keys = ($mapped = $this->getCookieKey($key)) ? $mapped : explode('.', $key);

    return $this->getDeepValue($this->_decode, $keys);
  }

  /**
   * @return bool
   */
  public function isHeadless()
  {
    return empty($_SERVER['REMOTE_ADDR']) && empty($this->getUserAgent()) && count($_SERVER['argv']) > 0;
  }

  /**
   * Returns TRUE if the cookie set by device.js is present, otherwise FALSE.
   *
   * @return bool
   */
  public function isHinted()
  {
    return isset($_COOKIE[$this->_cookie]);
  }

  /**
   * Returns the contents of the given server header. If the header matches an existing getter, results are returned
   * by that getter instead.
   *
   * @param string $key
   *
   * @return string|int
   */
  public function getHeader($key)
  {
    $method = 'get'.str_replace([' ', '_', '-', 'HTTP_'], '', ucwords($key, ' _-'));

    if (method_exists($this, $method))
    {
      return $this->$method();
    }
    else
    {
      $weird  = str_replace([' ', '-'], '_', strtoupper($key));
      $normal = (0 === strpos($weird, 'HTTP_')) ? $weird : 'HTTP_'.$weird;

      return (!empty($_SERVER[$normal])) ? $this->normalizeBoolean($_SERVER[$normal]) : null;
    }
  }

  // endregion ///////////////////////////////////////////// End General Getters

  // region //////////////////////////////////////////////// Specific Getters

  /**
   * Returns the client's user agent by checking various headers.  The first match found is returned.
   *
   * @return UserAgent|null
   */
  public function getUserAgent()
  {
    if (empty($this->_ua))
    {
      if ($ua = $this->getFirstHeaderMatch(self::HEADERS_USER_AGENT))
      {
        $this->_ua = new UserAgent($ua);
      }
    }

    return $this->_ua;
  }

  /**
   * Returns TRUE if a header implying that data should be saved is found, otherwise FALSE.
   *
   * @return bool|null
   */
  public function getSaveData()
  {
    if (is_null($this->_saveData))
    {
      $h = $this->getFirstHeaderMatch(self::HEADERS_SAVE_DATA);

      $this->_saveData = $this->normalizeBoolean($h, true);
    }

    return $this->_saveData;
  }

  // endregion ///////////////////////////////////////////// End Specific Getters

  /**
   * Recursively retrieves a value from the given nested array of values that matches the key in the given array of
   * keys.
   *
   * @param array $values
   * @param array $keys
   *
   * @return mixed|null
   */
  private function getDeepValue($values, $keys)
  {
    if (is_null($values))
    {
      return null;
    }
    foreach ($keys as $k)
    {
      if (array_key_exists($k, $values))
      {
        $values = $values[$k];
      }
      else
      {
        return null;
      }
    }

    return $values;
  }

  /**
   * Returns the first header from the given array of headers that is set in the $_SERVER array.
   *
   * @param array $headers
   *
   * @return mixed|null
   */
  protected function getFirstHeaderMatch($headers)
  {
    foreach ($headers as $header)
    {
      if (!empty($_SERVER[$header]))
      {
        return $_SERVER[$header];
      }
    }

    return null;
  }

  /**
   * Returns the cookie key that is mapped to the the given string key.  If the cookie key contains a period,
   * the key is returned as an array for convenience.
   *
   * @param string $key
   *
   * @return false|string[]|null
   */
  protected function getCookieKey($key)
  {
    $cKeys = [];
    $iKeys = is_array($key) ? $key : explode('.', $key);
    foreach ($iKeys as $key)
    {
      if (preg_match_all('#((?P<letters>[A-Za-z])[a-z]*)#', $key, $matches))
      {
        $cKeys[] = strtolower(implode('', $matches['letters']));
      }
    }

    return (!empty($cKeys)) ? $cKeys : null;
  }

  /**
   * @param string     $needle
   * @param array|null $regexMatches
   *
   * @return bool
   */
  protected function isUserAgentMatch($needle, &$regexMatches = null)
  {
    $needles = (is_array($needle)) ? $needle : [$needle];
    $ua      = $this->getUserAgent();

    foreach ($needles as $needle)
    {
      if (!is_null($regexMatches))
      {
        if (!preg_match($needle, $ua, $regexMatches))
        {
          return false;
        }
      }
      elseif (false === stripos($this->getUserAgent(), $needle))
      {
        return false;
      }
    }

    return true;
  }

  /**
   * Decodes the given string or array into an array of hints, first decoding any JSON string, then  normalizing 0 and
   * 1 to boolean values.  Code is safely wrapped in a try/catch to prevent malformed JSON from causing an exception.
   *
   * @param string|array $json_or_array
   *
   * @return array|null
   */
  protected function decodeHints($json_or_array = null)
  {
    $hints = (is_string($json_or_array)) ? json_decode($json_or_array, true) : $json_or_array;
    try
    {
      foreach ($hints as $key => $hint)
      {
        if (is_array($hint))
        {
          $hints[$key] = $this->decodeHints($hint);
        }
        elseif (0 == $hint)
        {
          $hints[$key] = false;
        }
        elseif (1 == $hint)
        {
          $hints[$key] = true;
        }
      }

      return $hints;
    }
    catch (\Exception $e)
    {
      return (is_array($json_or_array)) ? $json_or_array : null;
    }
  }

  /**
   * @param mixed $val
   * @param bool  $convertNumeric
   *
   * @return bool|int|mixed
   */
  protected function normalizeBoolean($val, $convertNumeric = false)
  {
    if (is_string($val))
    {
      $hl = strtolower($val);
      if (in_array($hl, ['on', 'true', 'yes']))
      {
        return true;
      }
      elseif (in_array($hl, ['off', 'false', 'no']))
      {
        return false;
      }
    }

    if ($convertNumeric)
    {
      if (0 === $val || '0' === $val)
      {
        return $val;
      }
      if (1 === $val || '1' === $val)
      {
        return $val;
      }
    }

    return $val;
  }

  protected function setDeepValue(&$values, $keys, $value)
  {
    $current = &$values;
    foreach ($keys as $key)
    {
      $current = &$current[$key];
    }
    $current = $value;

    return $this;
  }

  protected function setHintByKey($key, $value)
  {
    if (null !== $value)
    {
      $mapped = $this->getCookieKey($key);

      return $this->setDeepValue($this->_decode, $mapped, $value);
    }

    return $this;
  }
}
