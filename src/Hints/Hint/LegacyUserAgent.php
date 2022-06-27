<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\HeaderBagHint;
use DevCoding\Client\Object\Headers\UserAgentString as UserAgentObject;

/**
 * Returns the value for the USER_AGENT header, or polyfills the same using other headers likely to contain the user
 * agent string.
 *
 * Class LegacyUserAgent
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class LegacyUserAgent extends HeaderBagHint
{
  const KEY = 'User-Agent';

  /**
   * @return string
   */
  public function get()
  {
    $headers = [];
    foreach (UserAgentObject::HEADERS as $header)
    {
      $headers[] = str_replace('HTTP_', '', $header);
    }

    return $this->header($headers);
  }

  /**
   * @return string
   */
  public function getDefault()
  {
    return 'Mozilla/5.0 (Unknown) Unknown (Unknown)';
  }

  /**
   * @return bool
   */
  public function isNative()
  {
    return true;
  }

  /**
   * @return bool
   */
  public function isVendor()
  {
    return false;
  }

  /**
   * @return bool
   */
  public function isDraft()
  {
    return false;
  }

  /**
   * @return UserAgentObject|null
   */
  public function getObject()
  {
    return ($ua = $this->get()) ? new UserAgentObject($ua) : null;
  }

  public function isStatic()
  {
    return true;
  }
}
