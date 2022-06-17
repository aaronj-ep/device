<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Dependency\CookieBagAwareInterface;
use DevCoding\Helper\Dependency\CookieBagTrait;
use DevCoding\Hints\Base\HeaderBagHint;

/**
 * Returns the value for the Prefers-Reduced-Data secure client hint header, or polyfills the same. This indicates the
 * user's preference for using a reduced amount of data in each request.
 *
 * References:
 *   https://wicg.github.io/user-preference-media-features-headers/#sec-ch-prefers-reduced-data
 *   https://web.dev/user-preference-media-features-headers/
 *
 * Class ReducedData
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 * @package DevCoding\Hints
 */
class ReducedData extends HeaderBagHint implements CookieBagAwareInterface
{
  use CookieBagTrait;

  const KEY = 'Sec-CH-Prefers-Reduced-Data';

  /**
   * @return bool
   */
  public function get()
  {
    return $this->header(self::KEY) ?? $this->prefers(SaveData::KEY) ?? $this->getFallback() ?? $this->getDefault();
  }

  /**
   * @return bool
   */
  public function getDefault()
  {
    return false;
  }

  /**
   * @return bool
   */
  public function isDraft()
  {
    return true;
  }

  /**
   * @return bool
   */
  public function isNative()
  {
    return false;
  }

  /**
   * @return false
   */
  public function isStatic()
  {
    return false;
  }

  /**
   * @return bool
   */
  public function isVendor()
  {
    return true;
  }

  /**
   * @return bool|null
   */
  protected function getFallback()
  {
    return $this->isSlow() ?? $this->isMetered();
  }

  /**
   * Checks seldom used headers to determine if a connection is likely to be metered.  It is used exclusively as a
   * backup hint source in the 'isSaveData' method.
   *
   * This method is private due to the large number of false positives it may give. If you are on cellular & your
   * provider adds these headers, that does not necesssarily imply that you are on a metered plan.  In fact, due to
   * evil marketing, being on an 'unlimited' cellular connection doesn't even really mean that you aren't metered.
   *
   * @return bool
   */
  private function isMetered()
  {
    $m = ['HTTP_X_WAP_PROFILE', 'HTTP_ATT_DEVICEID', 'HTTP_WAP_CONNECTION', 'HTTP_X_ROAMING', 'HTTP_X_MOBILE_UA', 'HTTP_X_MOBILE_GATEWAY'];

    return !empty($this->header($m)) ? true : null;
  }

  /**
   * @return bool
   */
  private function isSlow()
  {
    $ect = $this->header(ECT::KEY) ?? $this->cookie(ECT::COOKIE);

    if (isset($ect))
    {
      return '3g' === $ect || '2g' === $ect || 'slow-2g' === $ect;
    }

    return null;
  }
}
