<?php
/**
 * DeviceFeatureInfo.php
 */

namespace XQ;

/**
 * Part of xqDetect v2.1.1 (https://github.com/exactquery/xq-detect)
 *
 * Provides basic information about the client device, as provided by javascript feature detection and stored in a
 * cookie.  If the cookie cannot be found (IE - Cookies or Javascript are disabled), some of the information is
 * detected from the UserAgent (which is not preferred).
 *
 * Class DeviceFeatureInfo
 *
 * @author  Aaron M Jones [aaron@jonesiscoding.com]
 * @licence MIT (https://github.com/exactquery/xq-detect/blob/master/LICENSE)
 * @package XQ/Detect;
 */
class DeviceFeatureInfo
{

  /** @var array                  Full info from d.js */
  protected $_detect = array();
  /** @var array                  Fallback defaults */
  protected $defaults = array(
    'hidpi' => false,
    'width' => 1024,
    'height' => 768,
    'low_speed' => false,
    'low_battery' => false,
    'metered' => false,
    'browser' => 'modern',
    'touch' => false,
    'android' => false,
    'ios' => false
  );

  protected $cookieName = 'EPVIEW';
  protected $DetectByUserAgent = null;

// region ///////////////////////////////////////////////// Getters/Setters

  /**
   * Retrieves the full results of the current client detection, or a specific parameter.  Available parameters are
   * hidpi, width, height, speed, modern, touch, and cookies.
   *
   * @param string|null $item The parameter you wish to detect
   *
   * @return string|array|bool          The detected parameter, or if $item was not given, the array of all parameters.
   */
  public function get($item = null)
  {
    if (empty($this->_detect)) {
      $this->detectParse();
    }

    if ($item) {
      if (isset($this->_detect[$item])) {
        return $this->_detect[$item];
      } else {
        return false;
      }
    }

    return $this->_detect;
  }

  /**
   * Returns the maximum width available in the user's device.  Please note that this does not indicate the current
   * size of the user's browser, but rather the maximum size it could be if they maximized the window.  It does not
   * account for things like scroll bars, etc.
   *
   * @return string
   */
  public function getDeviceMaxWidth()
  {
    return ($this->get('width')) ? $this->get('width') : $this->defaults['width'];
  }

  /**
   * Returns the maximum height available in the user's device.  Please note that this does not indicate the current
   * size of the user's browser, but rather the maximum size it could be if they maximized the window.  It does not
   * account for things like scroll bars, etc.
   *
   * @return int
   */
  public function getDeviceMaxHeight()
  {
    return ($this->get('height')) ? $this->get('height') : $this->defaults['height'];
  }

  public function getUserAgent()
  {
    return ($this->get('user-agent')) ? $this->get('user-agent') : null;
  }

  /**
   * If the browser is considered a 'baseline' browser based on the evaluation of it's HTML4/CSS2 capabilities.
   *
   * See 'isBrowserBaseline in d.js for more information on the tests performed.
   *
   * @return bool
   */
  public function isBaseline()
  {
    return ($this->get('browser') == "baseline") ? true : false;
  }

  /**
   * If the device has reported a low battery through the HTML5 battery status API.
   *
   * @return array|bool|string
   */
  public function isBatteryLow()
  {
    return $this->get('low_battery');
  }

  /**
   * If the browser is considered a 'fallback' browser based on the evaluation of it's HTML5/CSS3 capabilities.
   *
   * See 'isBrowserFallback' in d.js for more information on the tests performed.
   *
   * @return bool
   */
  public function isFallback()
  {
    return ($this->get('browser') == "fallback") ? true : false;
  }

  /**
   * If the browser reports a 2G, 3G, or less than 1Mbit connection through the Network Information API. The W3C
   * discontinued work on this specification on 4/10/2014.  Some mobile browsers still support it, so we will use it
   * if available.
   *
   * NOTE: This value is not updated if a user changes their connection during a session.
   *
   * @return bool
   */
  public function isLowSpeed()
  {
    return $this->get('low_speed');
  }

  /**
   * If the browser reports a metered connection through the Network Information API.  The W3C discontinued work on
   * this specification on 4/10/2014.  Some mobile browsers still support it, so we will use it if available.  It is
   * expected that a similar specification will be added in the future.
   *
   * NOTE: This value is not updated if a user changes their connection during a session.
   *
   * @return bool
   */
  public function isMetered()
  {
    return $this->get('metered');
  }

  /**
   * Determines if the user is using a client that utilizes a pixel density higher than 1, such as a hidpi windows
   * machine, a retina display mac, or a 2k/4k/5k monitor set to a scaled resolution.  This is useful for determining
   * what size images to send to the user, as 'normal' images are automatically scaled to double resolution on such
   * machines.  That can lead to some pretty ugly web images.
   *
   * @return  bool  TRUE if hidpi display, FALSE if not.
   */
  public function isHiDPI()
  {
    return $this->get('hidpi');
  }

  /**
   * Through some basic Javascript feature detection, it is evaluated whether or not the browser can support HTML4 or
   * HTML5.  Clients that cannot support either are determined to be "not modern".
   *
   * @return  bool TRUE by default, or FALSE if the browser is known to not support HTML4 or HTML5.
   */
  public function isModern()
  {
    return ($this->get('browser') == "modern") ? true : false;
  }

  /**
   * Through some basic JavaScript feature detection, it is determined whether the device being used has a touch screen.
   * Please note that the presence of a touch screen does not mean that the device is mobile, nor does it mean the user
   * uses the touch screen.  A good example of this would be a Windows 8.x laptop with a touch screen, in which the user
   * favors the mouse for input.
   *
   * @return  bool  FALSE by default, or TRUE if the device indicates that it supports touch capabilities.
   */
  public function isTouch()
  {
    return $this->get('touch');
  }

// endregion ////////////////////////////////////////////// End Getters/Setters

  /**
   * Alias for former method of getting detection information.
   * @deprecated
   * @return array|bool|string
   */
  public function getDetect()
  {
    return $this->get();
  }

  public function setDetect($detect)
  {
    if (!empty($detect) && is_array($detect)) {
      $this->_detect = array_merge($this->_detect, $detect);
    }

    return $this;
  }

  public function isDetected()
  {
    return isset($_COOKIE[$this->cookieName]);
  }

  public function isDetectedByUA()
  {
    return ($this->DetectByUserAgent instanceof DetectByUserAgent);
  }

  /**
   * Parses the cookie left by d.js.  If the cookie is not set due to Javascript being disabled, or cookies being
   * being blocked, all values are left at their (permissive) defaults, seen at the top of this class.
   */
  private function detectParse()
  {
    if ($this->isDetected()) {
      $x = json_decode($_COOKIE[$this->cookieName], true);

      if (!is_null($x)) {

        // Convert Boolean values from strings
        foreach ($x as $k => $v) {
          if ($v == "true") {
            $x[$k] = true;
          }
          if ($v == "false") {
            $x[$k] = false;
          }
        }

        $this->_detect = $x;
        $this->_detect['cookies'] = true;

      }
    } else {
      $this->_detect['cookies'] = (count($_COOKIE) > 0) ? true : false;

      // Attempt to get from User Agent
      $this->DetectByUserAgent = new DetectByUserAgent();
      $this->_detect = $this->DetectByUserAgent->detect();
    }

    // Append Defaults
    $this->_detect = $this->_detect + $this->defaults;
  }

}