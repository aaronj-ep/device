<?php
/**
 * ColorScheme.php
 */

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Dependency\CookieBagAwareInterface;
use DevCoding\Helper\Dependency\CookieBagTrait;
use DevCoding\Hints\Base\HeaderBagHint;

/**
 * Returns the value for the Prefers-Color-Scheme secure client hint header, or polyfills the same. This indicates the
 * user's preferred color scheme - dark or light.
 *
 * References:
 *   https://web.dev/prefers-color-scheme/
 *   https://wicg.github.io/user-preference-media-features-headers/#sec-ch-prefers-reduced-motion
 *
 * Class ColorScheme
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 * @package DevCoding\Hints
 */
class ColorScheme extends HeaderBagHint implements CookieBagAwareInterface
{
  use CookieBagTrait;

  const KEY     = 'Sec-CH-Prefers-Color-Scheme';
  const DEFAULT = 'light';
  const LIGHT   = 'light';
  const DARK    = 'dark';

  public function get()
  {
    return $this->prefers(self::KEY) ?? ($this->cookie('p.dm') ? 'dark' : $this->getDefault());
  }

  public function getDefault()
  {
    return self::DEFAULT;
  }

  public function isDraft()
  {
    return true;
  }

  public function isNative()
  {
    return false;
  }

  public function isStatic()
  {
    return false;
  }

  public function isVendor()
  {
    return false;
  }
}

