<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Dependency\CookieBagAwareInterface;
use DevCoding\Helper\Dependency\CookieBagTrait;

/**
 * Returns the value for the Save-Data secure client hint header, or polyfills the same. This indicates the
 * user's preference for using a reduced amount of data in each request.
 *
 * References:
 *   https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Save-Data
 *   https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/save-data
 *
 * Class SaveData
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 * @package DevCoding\Hints
 */
class SaveData extends ReducedData implements CookieBagAwareInterface
{
  use CookieBagTrait;

  const KEY = 'Save-Data';

  /**
   * @return bool
   */
  public function get()
  {
    return $this->header(static::KEY) ?? $this->prefers(ReducedData::KEY) ?? $this->getFallback() ?? $this->getDefault();
  }

  /**
   * @return bool
   */
  public function isDraft()
  {
    return false;
  }

  /**
   * @return bool
   */
  public function isNative()
  {
    return false;
  }

  public function isVendor()
  {
    return true;
  }
}
