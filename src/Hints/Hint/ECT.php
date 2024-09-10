<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;
use DevCoding\Hints\Base\CookieHintInterface;
use DevCoding\Hints\Base\CookieHintTrait;

/**
 * Returns the value for the ECT client hint header, or polyfills the same. This indicates an effective connection type.
 *
 * While the value could change during the user's session, for the purposes of server-side response, the value is
 * considered static for the entirety of a REQUEST.
 *
 * References:
 *   https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/ECT
 *   https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/client-hints#device_hints
 *   https://caniuse.com/mdn-http_headers_ect
 *
 * Class ECT
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/main/LICENSE)
 *
 * @package DevCoding\Hints
 */
class ECT extends Hint implements CookieHintInterface, ConstantAwareInterface
{
  use CookieHintTrait;

  const HEADER  = 'ECT';
  const COOKIE  = 'ect';
  const DEFAULT = '4g';
  const DRAFT   = false;
  const STATIC  = true;
  const VENDOR  = false;

  /**
   * @param string $ect
   *
   * @return bool
   */
  public static function isSlow(string $ect): bool
  {
    return '3g' === $ect || '2g' === $ect || 'slow-2g' === $ect;
  }
}
