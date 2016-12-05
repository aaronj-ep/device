<?php
/**
 * DetectByUserAgent.php
 */

namespace XQ;

/**
 * Part of xqDetect v2.2.0 (https://github.com/exactquery/xq-detect)
 *
 * Detects various conditions by using the user agent.  This is a really bad method to use.  This class is only
 * intended for use as a backup to the JS detection in d.js (used by DeviceFeatureInfo).  If you choose to use
 * it without the JS feature detection, hey, go for it.
 *
 * Class DetectByUserAgent
 *
 * @author  Aaron M Jones <aaron@jonesiscoding.com>
 * @licence MIT (https://github.com/exactquery/xq-detect/blob/master/LICENSE)
 * @package XQ/Detect;
 */
class DetectByUserAgent
{
  /** @var  string    The user agent string taken from the headers */
  protected $ua;
  /** @var  string    The version number detected */
  protected $version;
  /** @var  int       The major version number detected */
  protected $major;
  /** @var  int       The minor version number detected */
  protected $minor;
  /** @var  string    The revision version number detected */
  protected $rev;
  /** @var  string    The name of the browser detected, either msie, chrome, safari, firefox or edge. */
  protected $browser;
  /** @var  bool      If a mobile browser or device is detected */
  protected $mobile;
  /** @var  bool      If a touch device is detected */
  protected $touch;
  /** @var  bool      If an iOS device is detected */
  protected $ios;
  /** @var  bool      If an Android device is detected */
  protected $android;
  /** @var  bool      If a metered connection is detected */
  protected $metered;
  /** @var  bool      If a tablet or larger device is detected.  This is used to set a faux screen size only. */
  protected $tablet;
  /** @var  bool      If a phone sized device is detected.  This is used to set a faux screen size only. */
  protected $phone;
  /** @var  int       A faux width, based on the detection of tablet vs. phone.  Defaults to 1024 */
  protected $width;
  /** @var  int       A faux height, based on the detection of tablet vs. phone.  Defaults to 768 */
  protected $height;
  /** @var  bool      In the case of certain mobile devices, forces a 'baseline' browser (IE - IE8) */
  protected $forceBaseline;
  /** @var  bool      In the case of certain mobile devices, forces a 'fallback' browser (IE - IE9) */
  protected $forceFallback;
  /** @var array      Default sizes to use for width and height, since we can't actually detect via user agent.  */
  protected $defaultSize = array(
    'tablet' => array('width' => 1024, 'height' => 768),
    'phone' => array('width' => 320, 'height' => 460)
  );
  /** @var array      Possible headers that could contain the user agent, in order of precedence.  */
  public static $uaHeaders = array(
    'HTTP_USER_AGENT',
    'HTTP_X_OPERAMINI_PHONE_UA',
    'HTTP_X_DEVICE_USER_AGENT',
    'HTTP_X_ORIGINAL_USER_AGENT',
    'HTTP_X_SKYFIRE_PHONE',
    'HTTP_X_BOLT_PHONE_UA',
    'HTTP_DEVICE_STOCK_UA',
    'HTTP_X_UCBROWSER_DEVICE_UA'
  );
  /** @var array      Possible headers that indicate a mobile device */
  public static $mobileHeaders = array(
    'HTTP_X_WAP_PROFILE',
    'HTTP_X_WAP_PROFILE',
    'HTTP_ATT_DEVICEID',
    'HTTP_WAP_CONNECTION',
    'HTTP_X_ROAMING',
    'HTTP_X_MOBILE_UA',
    'HTTP_X_MOBILE_GATEWAY'
  );


  // region //////////////////////////////////////////////// Init

  /**
   * Sets the user agent from the headers.
   */
  public function __construct()
  {
    foreach ( self::$uaHeaders as $header )
    {
      if ( !empty( $_SERVER[ $header ] ) )
      {
        $this->ua = $_SERVER[ $header ];
        break;
      }
    }
  }

  /**
   * Detects the various items from the user agent, and returns what it can in the the array format desired by
   * DeviceFeatureInfo.  If nothing can be detected, returns null.
   *
   * @return array|null
   */
  public function detect()
  {
    $detected = true;
    if ( empty( $this->ua ) )
    {
      $detected = false;
    }
    else
    {
      // If it's not any of the common browsers, perhaps it's something mobile.
      if ( !$this->isMSIE() && !$this->isChrome() && !$this->isFirefox() && !$this->isSafari() && !$this->isEdge() && !$this->isOpera() )
      {
        if ( !$this->isOtherMobile() )
        {
          $detected = false;
        }
      }
      else
      {
        // If it's one of the default browsers, check to see if it's the mobile version.
        if ( !$this->isChromeMobile() && !$this->isSafariMobile() && !$this->isFirefoxMobile() && !$this->isOperaMobile() )
        {
          // If not, check for other mobile conditions, otherwise mark as definitely not mobile.
          if ( !$this->isOtherMobile() )
          {
            $this->mobile = false;
            $this->touch = false;
            $this->android = false;
            $this->ios = false;
            $this->tablet = false;
            $this->phone = false;
          }
        }
      }
    }

    if ( $detected )
    {
      // Deal with Width/Height
      if ( !$this->width )
      {
        $this->width = ( $this->phone ) ? $this->defaultSize[ 'phone' ][ 'width' ] : $this->defaultSize['tablet']['width'];
        $this->height = ( $this->phone ) ? $this->defaultSize[ 'phone' ][ 'height' ] : $this->defaultSize['tablet']['height'];
      }

      // Deal with Browser
      if ( $this->isModern() )
      {
        $browser = 'modern';
      }
      elseif($this->isFallback())
      {
        $browser = 'fallback';
      }
      else
      {
        $browser = 'baseline';
      }

      // Return the array that DeviceFeatureInfo expects.
      return array(
        'hidpi'       => false,
        'width'       => $this->width,
        'height'      => $this->height,
        'low_speed'   => false,
        'low_battery' => false,
        'metered'     => $this->metered,
        'browser'     => $browser,
        'touch'       => $this->touch,
        'android'     => $this->android,
        'ios'         => $this->ios,
        'user-agent'  => $this->ua
      );
    }

    return null;
  }

  // endregion ///////////////////////////////////////////// End Init

  // region //////////////////////////////////////////////// Browser Classification Methods

  /**
   * As with d.js, a 'modern' browser is defined here as one that supports FlexBox with the 'current' syntax, including
   * the fringe case of IE10.  Version numbers below taken from 'caniuse.com' results for Flexbox features.
   *
   * @return bool
   */
  private function isModern()
  {
    if ( !$this->forceBaseline && !$this->forceFallback )
    {
      switch ( $this->browser )
      {
        case "edge":
          return true;
        case 'msie':
          return ($this->major > 9);
          break;
        case 'chrome':
          return ( $this->major > 20 );
          break;
        case 'firefox':
          return ( $this->major > 27 );
          break;
        case 'opera':
          if ( $this->mobile )
          {
            return ( $this->major > 12 || ( $this->major == 11 && $this->minor >= 1 ) );
          }
          else
          {
            return ( $this->major > 11 || ( $this->major == 11 && $this->minor > 4 ) );
          }
          break;
        case 'safari':
          if(!$this->ios)
          {
            return ( $this->major > 6 || ( $this->major == 6 && $this->minor >= 1 ) );
          }
          else
          {
            return ( $this->major > 7 || ( $this->major == 7 && $this->minor >= 0 ) );
          }
          break;
        default:
          if ( $this->mobile )
          {
            if ( $this->android )
            {
              return ( $this->major > 4 || ( $this->major == 4 && $this->minor >= 4 ) );
            }
          }
      }
    }

    return false;
  }

  /**
   * As with d.js, a 'fallback' browser is defined as one that can use localStorage, opacity, border-radius and media
   * queries.  Version numbers below taken from 'caniuse.com' results for these features.
   *
   * @return bool
   */
  private function isFallback()
  {
    if ( $this->forceFallback )
    {
      return true;
    }
    else
    {
      if ( !$this->isModern() && !$this->forceBaseline )
      {
        switch ( $this->browser )
        {
          case "msie":
            return ( $this->major > 8 );
          case "chrome":
            return ( $this->major > 29 );
          case "safari":
            return ( $this->major > 4 );
          case "firefox":
            return ( $this->major > 15 );
          case "opera":
            return ( $this->major > 12 || ( $this->major == 12 && $this->minor >= 1 ) );
          default:
            if ( $this->mobile && $this->android )
            {
              return ( $this->major > 4 || ( $this->major == 4 && $this->minor >= 4 ) );
            }
            break;
        }
      }
    }

    return false;
  }

  // endregion ///////////////////////////////////////////// End Browser Classification Methods

  // region //////////////////////////////////////////////// Browser Detection Methods

  /**
   * Determines if a browser is Safari by the user agent string.
   *
   * @return bool
   */
  private function isSafari()
  {
    $is = false;
    if ( preg_match( "/(Version)\/(\d+)\.(\d+)(?:\.(\d+))?.*Safari\//", $this->ua, $matches ) )
    {
      // These things sometimes surf around acting like they're Safari.
      if ( !preg_match( "/(PhantomJS|Silk|rekonq|OPR|Chrome|Android|Edge|bot)/", $this->ua ) )
      {
        $is = true;
        $major = ( isset( $matches[ 2 ] ) ) ? $matches[ 2 ] : null;
        $minor = ( isset( $matches[ 3 ] ) ) ? $matches[ 3 ] : null;
        $rev = ( isset( $matches[ 4 ] ) ) ? $matches[ 4 ] : null;
        $version = ( isset( $major ) ) ? $major : null;
        $version .= ( isset( $minor ) ) ? '.' . $minor : null;
        $version .= ( isset( $rev ) ) ? '.' . $rev : null;
      }
    }
    // Versions below 3.x doe not have the Version/ in the UA.  Since we can't get the version number, we go with the
    // highest version that didn't have the version number in the UA.  It doesn't really matter, since this would be
    // a 'baseline' browser anyway.
    elseif ( preg_match("/(Safari)\/\d+/", $this->ua, $matches) )
    {
      // These things sometimes surf around acting like they're Safari.
      if ( !preg_match( "/(PhantomJS|Silk|rekonq|OPR|Chrome|Android|Edge|bot)/", $this->ua ) )
      {
        $is = true;
        $version = 2;
        $major = 2;
        $minor = 0;
        $rev = 4;
      }
    }

    if ( $is && isset( $version, $major, $minor ) )
    {
      $this->browser = 'safari';
      $this->version = $version;
      $this->major = $major;
      $this->minor = $minor;
      $this->rev = ( isset( $rev ) ) ? $rev : null;

      return true;
    }

    return false;
  }

  /**
   * Determines if a browser is chrome by the user agent string.
   * @return bool
   */
  private function isChrome()
  {
    $is = false;
    // For our purposes, Chrome and Chromium are the same.
    if ( preg_match( "/(Chrome|Chromium)\/([0-9\.]+)/", $this->ua, $matches ) )
    {
      // These things go around acting like they are chrome, but they aren't.
      if ( !preg_match( "/(MRCHROME|FlyFlow|baidubrowser|bot|Edge)/i", $this->ua ) )
      {
        $is = true;
        $version = $matches[ 2 ];
        $parts = explode( '.', $matches[ 2 ] );
        $major = array_shift($parts);
        $minor = array_shift($parts);
        $rev = implode( '.', $parts );
      }
    }

    if ( $is && isset($version,$major,$minor,$rev) )
    {
      $this->browser = 'chrome';
      $this->version = $version;
      $this->major = $major;
      $this->minor = $minor;
      $this->rev = $rev;

      return true;
    }

    return false;
  }

  /**
   * Determines if a browser is Microsoft Edge by the user agent string.
   *
   * @return bool
   */
  private function isEdge()
  {
    $is = false;
    if ( preg_match("/Edge (([0-9]+)\.?([0-9]*))/", $this->ua, $matches) )
    {
      $is = true;
      $version = $matches[ 1 ];
      $major = $matches[ 2 ];
      $minor = $matches[ 3 ];
    }

    // Nothing is known to try to look like a faux Edge, but we'll still exclude 'bots'
    if ( $is && isset($version,$major,$minor) && stripos($this->ua,'bot') === false)
    {
      $this->browser = "edge";
      $this->version = $version;
      $this->major = $major;
      $this->minor = $minor;

      return true;
    }

    return false;
  }

  /**
   * Determines if a browser is Internet Explorer by the user agent string.
   *
   * @return bool
   */
  private function isMSIE()
  {
    $is = false;
    // Versions prior to IE11 follow this format
    if ( preg_match("/MSIE (([0-9]+)\.?([0-9]*))/", $this->ua, $matches) )
    {
      $is = true;
      $version = $matches[ 1 ];
      $major = $matches[ 2 ];
      $minor = $matches[ 3 ];
    }
    // This one is for IE11 only.
    elseif ( preg_match( "/Trident\/[0-9]\.[0-9]; [^;]*[;\s]*rv:(([0-9]+)\.?([0-9]*))/", $this->ua, $matches ) )
    {
      $is = true;
      $version = $matches[ 1 ];
      $major = $matches[ 2 ];
      $minor = $matches[ 3 ];
    }

    // Excludes 'bots' that might be acting like MSIE.
    if ( $is && isset($version,$major,$minor) && stripos($this->ua,'bot') === false)
    {
      $this->browser = "msie";
      $this->version = $version;
      $this->major = $major;
      $this->minor = $minor;

      return true;
    }

    return false;
  }

  /**
   * Determines if a browser is Opera by the user agent string.
   *
   * @return bool
   */
  private function isOpera()
  {
    $is = false;
    // The 'Opera/x.xx' stopped at 9.80, then the version went into the 'Version/x.x.x' format.
    if ( preg_match("/(Opera)\/9.80.*Version\/((\d+)\.(\d+)(?:\.(\d+))?)/", $this->ua, $matches) )
    {
      $is = true;
      $version = $matches[ 2 ];
      $major = $matches[ 3 ];
      $minor = $matches[ 4 ];
    }
    // Earlier versions had the version in 'Opera/x.xx' format.
    elseif ( preg_match( "/Opera (([0-9]+)\.?([0-9]*))/", $this->ua, $matches ) )
    {
      $is = true;
      $version = $matches[ 1 ];
      $major = $matches[ 2 ];
      $minor = $matches[ 3 ];
    }
    // Some versions of Opera Mobile look like this.  Luckily it's got OPR in the string or we'd think it was Safari.
    elseif ( preg_match( "/(?:Mobile Safari).*(OPR)\/(\d+)\.(\d+)\.(\d+)/", $this->ua, $matches ) )
    {
      $is = true;
      $version = $matches[ 2 ];
      $major = $matches[ 3 ];
      $minor = $matches[ 4 ];
    }
    // Opera version 15 was a freak UA, looking like Chrome, but with OPR in the string.
    elseif ( preg_match( "/(?:Chrome).*(OPR)\/(\d+)\.(\d+)\.(\d+)/", $this->ua, $matches ) )
    {
      $is = true;
      $version = $matches[ 2 ];
      $major = $matches[ 3 ];
      $minor = $matches[ 4 ];
    }

    if ( $is && isset( $version, $major, $minor ) )
    {
      $this->browser = "opera";
      $this->version = $version;
      $this->major = $major;
      $this->minor = $minor;

      return true;
    }

    return false;
  }

  /**
   * Determines if a browser is Firefox by the User Agent string.
   *
   * @return bool
   */
  private function isFirefox()
  {
    $is = false;

    // Wow, firefox could look like ANY of these things.  Most are mobile.
    if ( preg_match( "/(Firefox|Fennec|Namoroka|Shiretoko|Minefield|MozillaDeveloperPreview)\/([^\s^;^)]+)/", $this->ua, $matches ) )
    {
      // If it says this, it's just a faker, not really firefox.
      if ( !preg_match( "/(bot|MSIE|HbbTV|Chimera|Seamonkey|Camino)/i", $this->ua ) )
      {
        $is = true;
        $version = $matches[ 2 ];
        $parts = explode( '.', $matches[ 2 ] );
        $major = array_shift($parts);
        $minor = array_shift($parts);
        $rev = implode( '.', $parts );
      }
    }

    if ( $is && isset($version,$major,$minor,$rev) )
    {
      $this->browser = 'firefox';
      $this->version = $version;
      $this->major = $major;
      $this->minor = $minor;
      $this->rev = $rev;

      return true;
    }

    return false;

  }

  // endregion ///////////////////////////////////////////// End Browser Detection Methods

  // region //////////////////////////////////////////////// Mobile Browser Detection Methods

  /**
   * Determines if the browser is the mobile or mini version of opera by the user agent string.
   *
   * @return bool
   */
  private function isOperaMobile()
  {
    $is = false;
    $android = false;
    $ios = false;
    $tablet = false;
    $phone = false;
    $touch = false;

    if ( $this->browser == 'opera' || $this->isOpera() )
    {
      // This is definitely opera mobile, strangely, on Android
      if ( preg_match( "/(?:Mobile Safari).*(OPR)/", $this->ua ) )
      {
        $is = true;
        $android = true;
      }
      // Ok, perhaps we can find these things in the UA giving us a clue.
      elseif ( preg_match( '/(mobi|mini)/i', $this->ua ) )
      {
        $is = true;
        // Header set by Opera Mini only.  No longer used, but older versions still have it.
        if ( isset( $_SERVER[ 'HTTP_X_OPERAMINI_PHONE_UA' ] ) )
        {
          $phone = true;
          $tablet = false;
          $android = ( isset( $_SERVER[ 'Device-Stock-UA' ] ) && stripos( $_SERVER[ 'Device-Stock-UA' ], 'android' ) !== false ) ? true : false;
          $touch = ( $android || stripos( $_SERVER[ 'Device-Stock-UA' ], 'touch' ) !== false ) ? true : false;
        }
        // Header set by Opera Mini and Mobile with the original Device's User Agent.  We can get some extra info from this.
        elseif ( isset( $_SERVER[ 'Device-Stock-UA' ] ) )
        {
          $tablet = ( stripos( $_SERVER[ 'Device-Stock-UA' ], 'tablet' ) !== false ) ? true : false;
          $phone = ( !$tablet && ( stripos( $_SERVER[ 'Device-Stock-UA' ], 'phone' ) !== false ) ) ? true : false;
          $android = ( stripos( $_SERVER[ 'Device-Stock-UA' ], 'android' ) !== false ) ? true : false;
          $touch = ( $android || stripos( $_SERVER[ 'Device-Stock-UA' ], 'touch' ) !== false ) ? true : false;
        }
        // No extra info available, let's see what we can still find out. We'll assume it's a phone if it does say tablet.
        else
        {
          $tablet = ( stripos( $this->ua, 'tablet' ) !== false ) ? true : false;
          $phone = ( $tablet ) ? false : true;
          if ( $tablet || stripos( $this->ua, 'mini' ) === false )
          {
            $touch = true;
          }
        }
      }
    }

    if ( $is )
    {
      $this->mobile = true;
      $this->android = $android;
      $this->ios = $ios;
      $this->tablet = $tablet;
      $this->phone = $phone;
      $this->touch = $touch;
    }

    return $is;
  }

  /**
   * Determines if the browser is Chrome on a mobile device by the user agent string.
   *
   * @return bool
   */
  private function isChromeMobile()
  {
    $is = false;
    $android = false;
    $ios = false;
    $tablet = false;
    $phone = false;
    if ( $this->browser == 'chrome' || $this->isChrome() )
    {
      // We aren't differentiating between Chrome and webview.  It doesn't matter for our purposes.
      if ( stripos( $this->ua, 'android' ) !== false )
      {
        $is = true;
        $android = true;
        $tablet = ( stripos( $this->ua, 'mobile' ) !== false ) ? false : true;
        $phone = ($tablet) ? false : true;
      }
      // Only says this on iOS.  Where it's really Safari.  Yuck.
      elseif ( strpos( $this->ua, 'CriOS' ) !== false )
      {
        $is = true;
        $ios = true;
        $phone = preg_match("/(iPhone|iPod)/", $this->ua );
        $tablet = (strpos($this->ua, 'iPad') !== false) ? true : false;
      }
    }

    if ( $is )
    {
      $this->android = $android;
      $this->ios = $ios;
      $this->tablet = $tablet;
      $this->phone = $phone;
      $this->mobile = true;
      $this->touch = true;
    }

    return $is;
  }

  /**
   * Determines if the browser is Safari Mobile by the User Agent String.
   *
   * @return bool
   */
  private function isSafariMobile()
  {
    $is = false;
    $tablet = false;
    $phone = false;
    if ( $this->browser == 'safari' || $this->isSafari() )
    {
      // We aren't differentiating between Safari and Apps. It doesn't matter for our purposes.
      // We also don't care if it's an iPod or iPhone, it's the size we care about.
      if ( preg_match("/(iPhone|iPod|iPad)/", $this->ua, $matches ) )
      {
        $is = true;
        $tablet = ( $matches[1] == 'iPad' );
        $phone = ($matches[1] == 'iPhone' || $matches[1] == 'iPod');
      }
    }

    if ( $is )
    {
      $this->android = false;
      $this->ios = true;
      $this->tablet = $tablet;
      $this->phone = $phone;
      $this->mobile = true;
      $this->touch = true;
    }

    return $is;
  }

  /**
   * Determines if the browser is Firefox Mobile, by the User Agent string.
   *
   * @return bool
   */
  private function isFirefoxMobile()
  {
    $is = false;
    $tablet = false;
    $phone = false;
    $android = false;
    $touch = true;
    if ( $this->browser == 'firefox' || $this->isFirefox() )
    {
      if ( preg_match( "/(Mobile|Tablet|TV)/", $this->ua, $matches ) )
      {
        $is = true;
        $tablet = ($matches[1] == 'Tablet' || $matches[1] == 'TV') ? true : false;
        $phone =  ($matches[1] == 'Mobile') ? true : false;
      }
      elseif ( stripos( $this->ua, 'Android' ) !== false )
      {
        // No way to determine phone/tablet
        $is = true;
        $android = true;
      }
      elseif ( stripos( $this->ua, 'Maemo' ) !== false )
      {
        $is = true;
        // It's horizontal, which is probably more like a tablet, but it's only 800x480.
        $phone = true;
        $android = false;
        // Yes, the Maemo units have a touch screen, but it's resistive, and doesn't really function like we think
        // of touch in a current web environment.
        $touch = false;
      }
      elseif ( stripos( $this->ua, 'Fennec' ) !== false )
      {
        // No way to determine phone/tablet OR OS
        $is = true;
      }
    }

    if ( $is )
    {
      $this->android = $android;
      // No Firefox for iOS
      $this->ios = false;
      $this->tablet = $tablet;
      $this->phone = $phone;
      $this->mobile = true;
      $this->touch = $touch;
    }

    return $is;
  }

  // endregion ///////////////////////////////////////////// End Mobile Browser Detection Methods

  // region //////////////////////////////////////////////// Mobile Device Detection Methods

  /**
   * This is just basic mobile device detection, and quite likely to be wrong in it's assumptions.  It's only really used
   * after everything else fails.
   *
   * @return bool
   */
  private function isOtherMobile()
  {
    // Defaults to a mystery OS, tablet sized device, with touchscreen
    $is = false;
    $android = false;
    $ios = false;
    $touch = true;
    $phone = false;
    $tablet = true;
    $baseline = false;
    $fallback = false;
    $metered = false;

    if ( preg_match( "/iP(hone|od|ad)|Android|BlackBerry|IEMobile|Kindle|NetFront|Silk-Accelerated|(hpw|web)OS|Fennec|Minimo|Opera M(obi|ini)|Blazer|Dolfin|Dolphin|Skyfire|Zune|Bolt/", $this->ua, $matches ) )
    {
      $is = true;
      switch ( $matches[ 0 ] )
      {
        case 'iPhone':
        case 'iPod':
          $android = false;
          $ios = true;
          $phone = true;
          $tablet = false;
          break;
        case 'iPad':
          $android = false;
          $ios = true;
          $tablet = true;
          $phone = false;
          break;
        case 'Android':
          $android = true;
          $ios = false;
          if ( preg_match( "/Android ([0-9\.]+)/", $this->ua, $matches ) )
          {
            $version = $matches[ 1 ];
            $parts = explode( '.', $version );
            $major = array_shift($parts);
            $minor = array_shift($parts);
            $rev = implode( '.', $parts );

            // Android supports flexbox as of 4.4
            if ( $major < 4 || ($major == 4 && $minor < 4 ) )
            {
              // Android supports media queries as of 3.0
              if($major < 3)
              {
                $baseline = true;
              }
              else
              {
                $fallback = true;
              }
            }
          }
          break;
        case 'Kindle':
        case 'SilkAccelerated':
          $android = true;
          $phone = false;
          break;
        case 'hpwOS':
          // Most of these are actually printers or TVs.  We'll call them tablets.
          $tablet = true;
          $phone = false;
          break;
        case 'webOS':
          $phone = true;
          $tablet = false;
          break;
        case 'IEMobile':
          $android = false;
          $ios = false;
          $phone = true;
          $tablet = false;
          $fallback = true;
          break;
        case 'Blazer':
        case 'Zune':
        case 'Blackberry':
          $touch = false;
          $android = false;
          $ios = false;
          $phone = true;
          $baseline = true;
          break;
        default:
          // These would be old things, we're going to go with a phone UI, no touch, mystery OS.
          $touch = false;
          $android = false;
          $ios = false;
          $phone = true;
          $tablet = false;
          $baseline = true;
          break;
      }
    }
    // This is the classic short detection.  The only reason it's not used first is it's less likely to give us any
    // additional details, like the above Regex does.
    elseif ( preg_match( '/(mobi|phone)/i', $this->ua ) )
    {
      $is = true;
      $touch = true;
      $tablet = (stripos($this->ua, 'tablet') !== false) ? true : false;
      $phone = ($tablet) ? false : true;
    }
    // Hey, if it says tablet, who are we to judge?
    elseif ( stripos( $this->ua, 'tablet' ) )
    {
      $is = true;
      $touch = true;
      $tablet = true;
      $phone = false;
    }
    // If it's a TV, we at least know that it isn't phone sized.
    elseif ( stripos( $this->ua, 'tv' ) )
    {
      $is = true;
      $touch = false;
      $tablet = true;
      $phone = false;
    }
    else
    {
      // If these headers are set then it's definitely a mobile device of some sort, likely on cellular.
      foreach ( self::$mobileHeaders as $header )
      {
        if ( array_key_exists( $header, $_SERVER ) )
        {
          $is = true;
          $tablet = false;
          $phone = true;
          $metered = true;
          break;
        }
      }
    }

    if ( $is )
    {
      $this->mobile = true;
      $this->android = $android;
      $this->ios = $ios;
      $this->tablet = $tablet;
      $this->phone = $phone;
      $this->touch = $touch;
      $this->metered = $metered;
      $this->forceFallback = $fallback;
      $this->forceBaseline = $baseline;

      if ( isset( $version, $major, $minor ) )
      {
        $this->version = $version;
        $this->major = $major;
        $this->minor = $minor;
        $this->rev = ( isset( $rev ) ) ? $rev : null;
      }
    }

    return $is;
  }

  // endregion ///////////////////////////////////////////// End Mobile Device Detection Methods

}