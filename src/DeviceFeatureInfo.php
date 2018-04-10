<?php
/**
 * DeviceFeatureInfo.php
 */

namespace XQ;

/**
 * Part of xqDetect v3.0.2 (https://github.com/exactquery/xq-detect)
 *
 * Provides basic information about the client device, as provided by javascript feature detection and stored in a
 * cookie.  If the cookie cannot be found (IE - Cookies or Javascript are disabled), some of the information is
 * detected from the UserAgent (which is not preferred).
 *
 * Class DeviceFeatureInfo
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/exactquery/xq-detect/blob/master/LICENSE)
 * @package XQ/Detect;
 */
class DeviceFeatureInfo extends DetectDefaults
{

  /** @var array                  Full info from d.js */
  protected $_detect = array();
  /** @var string */
  protected $cookieName = '';
  /** @var DetectByUserAgent */
  protected $DetectByUserAgent = null;

  /**
   * @param string $cookieName
   */
  public function __construct( $cookieName = 'djs' )
  {
    $this->cookieName = $cookieName;
  }

  // region ///////////////////////////////////////////////// Device Info

  /**
   * Retrieves the full results of the current client detection, or a specific parameter.  Available parameters are
   * hidpi, width, height, speed, modern, touch, and cookies.
   *
   * @param string|null $item The parameter you wish to detect
   *
   * @return string|array|bool          The detected parameter, or if $item was not given, the array of all parameters.
   */
  public function get( $item = null )
  {
    if( empty( $this->_detect ) || !array_key_exists( $item, $this->_detect ) )
    {
      $this->_detect = $this->detect();
    }

    if( $item )
    {
      return ( array_key_exists( $item, $this->_detect ) ) ? $this->_detect[ $item ] : false;
    }

    return $this->_detect;
  }

  /**
   * Returns the maximum width available in the user's device.  Please note that this does not indicate the current
   * size of the user's browser, but rather the maximum size it could be if they maximized the window.  It does not
   * account for things like scroll bars, etc.
   *
   * @return int
   */
  public function getMaxWidth()
  {
    return ( !empty( $this->get( 'width' ) ) ) ? (int)$this->get( 'width' ) : self::WIDTH;
  }

  /**
   * Returns the maximum height available in the user's device.  Please note that this does not indicate the current
   * size of the user's browser, but rather the maximum size it could be if they maximized the window.  It does not
   * account for things like scroll bars, etc.
   *
   * @return int
   */
  public function getMaxHeight()
  {
    return ( !empty( $this->get( 'height' ) ) ) ? (int)$this->get( 'height' ) : self::HEIGHT;
  }

  /**
   * @return string|null
   */
  public function getUserAgent()
  {
    return ( $this->get( 'user-agent' ) ) ? $this->get( 'user-agent' ) : null;
  }

  /**
   * If the browser is considered a 'baseline' browser based on the evaluation of it's HTML5/CSS2 capabilities.
   *
   * See 'isBaseline' in d.js for more information on the tests performed.
   *
   * @return bool
   */
  public function isBaseline()
  {
    return ( $this->get( 'browser' ) == "baseline" ) ? true : false;
  }

  /**
   * If the browser is considered a 'fallback' browser based on the evaluation of it's Media Query 4 capabilities.
   *
   * See 'isFallback' in d.js for more information on the tests performed.
   *
   * @return bool
   */
  public function isFallback()
  {
    return ( $this->get( 'browser' ) == "fallback" ) ? true : false;
  }

  /**
   * This method uses two criteria:
   *
   *   * The 'save-data' header added by Chrome 49+ (mobile), or Chrome Data Saver extension, Opera 35+, Yandex 16.2+
   *   * The Network Information API (discontinued by W3C on 4/10/2014, still supported by some browsers)
   *
   * NOTE: This value is not updated if a user changes their connection or preference during a session.
   *
   * @return bool
   */
  public function isMetered()
  {
    return $this->get( 'metered' );
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
    return $this->get( 'hidpi' );
  }

  /**
   * Through some basic Javascript feature detection, it is evaluated whether or not the browser can support HTML4 or
   * HTML5.  Clients that cannot support either are determined to be "not modern".
   *
   * @return  bool TRUE by default, or FALSE if the browser is known to not support HTML4 or HTML5.
   */
  public function isModern()
  {
    return ( $this->get( 'browser' ) == "modern" ) ? true : false;
  }

  /**
   * @return array|bool|string
   */
  public function isRetina()
  {
    return $this->get( 'hidpi' );
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
    return $this->get( 'touch' );
  }

  // endregion ////////////////////////////////////////////// End Getters/Setters

  /**
   * @return bool
   */
  public function isDetected()
  {
    return isset( $_COOKIE[ $this->cookieName ] );
  }

  /**
   * @return bool
   */
  public function isDetectedByUA()
  {
    return ( $this->DetectByUserAgent instanceof DetectByUserAgent );
  }

  /**
   * Parses the cookie left by d.js.  If the cookie is not set due to Javascript being disabled, or cookies being
   * being blocked, DetectByUserAgent is used to determine the values by the user agent.
   *
   * Using the user agent can be quite flawed. As we are limiting it to feature detection, it makes a good backup here.
   *
   * @return array
   */
  private function detect()
  {
    $detect   = array();
    $defaults = $this->getDefaults();
    if( $this->isDetected() )
    {
      $detect = json_decode( $_COOKIE[ $this->cookieName ], true );

      if( !is_null( $detect ) )
      {
        // Convert Boolean values from strings
        foreach( $detect as $k => $v )
        {
          if( $v == "true" )
          {
            $detect[ $k ] = true;
          }
          if( $v == "false" )
          {
            $detect[ $k ] = false;
          }

          // Allow Server Overrides
          if ( $v === false && in_array( $k, DetectDefaults::SERVER ) ) {
            $detect[$k] = ( array_key_exists( $k, $defaults ) ) ? $defaults[ $k ] : $v;
          }
        }
      }
    }
    elseif( !$this->DetectByUserAgent instanceof DetectByUserAgent )
    {
      // Attempt to get from User Agent
      $this->DetectByUserAgent = new DetectByUserAgent();
      $detect                  = $this->DetectByUserAgent->detect();
    }

    // Append Defaults
    return ( !empty( $detect ) ) ? array_merge( $defaults, $detect ) : $defaults;
  }
}