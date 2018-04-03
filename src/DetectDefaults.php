<?php
/**
 * DetectDefaults.php
 */

namespace XQ;

/**
 * Part of xqDetect v3.0 https://github.com/exactquery/xq-detect)
 *
 * Class DetectDefaults
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/exactquery/xq-detect/blob/master/LICENSE)
 * @package XQ/Detect;
 */
class DetectDefaults
{
  const VIEWPORT_WIDTH = 1024;
  const WIDTH = 1024;
  const HEIGHT = 768;
  const TOUCH = false;
  const BROWSER = 'modern';
  const SERVER = array('cookies', 'hidpi', 'metered', 'user-agent', 'viewport');

  const USER_AGENT_HEADERS = array(
    'HTTP_USER_AGENT',
    'HTTP_X_OPERAMINI_PHONE_UA',
    'HTTP_X_DEVICE_USER_AGENT',
    'HTTP_X_ORIGINAL_USER_AGENT',
    'HTTP_X_SKYFIRE_PHONE',
    'HTTP_X_BOLT_PHONE_UA',
    'HTTP_DEVICE_STOCK_UA',
    'HTTP_X_UCBROWSER_DEVICE_UA'
  );

  const METERED_HEADERS = array(
    'HTTP_SAVE_DATA',
    'HTTP_X_WAP_PROFILE',
    'HTTP_X_WAP_PROFILE',
    'HTTP_ATT_DEVICEID',
    'HTTP_WAP_CONNECTION',
    'HTTP_X_ROAMING',
    'HTTP_X_MOBILE_UA',
    'HTTP_X_MOBILE_GATEWAY'
  );

  /**
   * These defaults come from an number of places, including client hints.  The only values based on a UA string are
   * the Android & iOS values.
   *
   * References:
   *   * https://developers.google.com/web/updates/2015/09/automating-resource-selection-with-client-hints
   *   * http://httpwg.org/http-extensions/client-hints.html
   *   * https://developers.google.com/web/updates/2016/02/save-data
   *
   * @return array
   */
  public function getDefaults()
  {
    return array(
      'android'     => false,
      'browser'     => self::BROWSER,
      'cookies'     => ( count( $_COOKIE ) > 0 ) ? true : false,
      'height'      => self::HEIGHT,
      'hidpi'       => ( array_key_exists( 'HTTP_DPR', $_SERVER ) ) ? $_SERVER[ 'HTTP_DPR' ] > 1 : false,
      'ios'         => false,
      'low_speed'   => false, // deprecated
      'low_battery' => false, // deprecated
      'metered'     => $this->getMeteredDefault(),
      'touch'       => self::TOUCH,
      'user-agent'  => $this->getUserAgentDefault(),
      'viewport'    => $this->getViewportDefault(),
      'width'       => self::WIDTH
    );
  }

  /**
   * Uses potential mobile headers & Google's new 'save-data' header to determine if we have a metered connection.
   *
   *    * Reference for 'save-data': https://developers.google.com/web/updates/2016/02/save-data
   *
   * @return bool
   */
  private function getMeteredDefault()
  {
    foreach( self::METERED_HEADERS as $header )
    {
      if( array_key_exists( $header, $_SERVER ) )
      {
        return true;
      }
    }

    return false;
  }

  /**
   * References:
   *   * https://developers.google.com/web/updates/2015/09/automating-resource-selection-with-client-hints
   *   * http://httpwg.org/http-extensions/client-hints.html
   *
   * @return int
   */
  private function getViewportDefault()
  {
    return ( array_key_exists( 'HTTP_VIEWPORT_WIDTH', $_SERVER ) ) ? $_SERVER[ 'HTTP_VIEWPORT_WIDTH' ] : self::VIEWPORT_WIDTH;
  }

  protected function getUserAgentDefault()
  {
    foreach( self::USER_AGENT_HEADERS as $header )
    {
      if( !empty( $_SERVER[ $header ] ) )
      {
        return $_SERVER[ $header ];
      }
    }

    return null;
  }
}