<?php
/**
 * ColorScheme.php
 */

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;
use DevCoding\Hints\Base\CookieHintInterface;
use DevCoding\Hints\Base\CookieHintTrait;

/**
 * Returns the value for the Prefers-Color-Scheme secure client hint header, or polyfills the same. This indicates the
 * user's preferred color scheme - dark or light.
 *
 * References:
 *   https://web.dev/prefers-color-scheme/
 *   https://wicg.github.io/user-preference-media-features-headers/#sec-ch-prefers-color-scheme
 *   https://caniuse.com/mdn-http_headers_sec-ch-prefers-color-scheme
 *
 * Class ColorScheme
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/main/LICENSE)
 * @package DevCoding\Hints
 */
class ColorScheme extends Hint implements ConstantAwareInterface, CookieHintInterface
{
  use CookieHintTrait;

  const LIGHT   = 'light';
  const DARK    = 'dark';
  const HEADER  = 'Sec-CH-Prefers-Color-Scheme';
  const COOKIE  = 'pcs';
  const DEFAULT = self::LIGHT;
  const DRAFT   = true;
  const STATIC  = false;
  const VENDOR  = false;
}

