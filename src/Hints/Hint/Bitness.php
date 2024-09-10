<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;

/**
 * Returns the value for the Sec-CH-UA-Bitness client hint header, or polyfills the same. This indicates the bitness of
 * the architecture of the platform on which a given user agent is executing.
 *
 * References:
 *  https://wicg.github.io/ua-client-hints/#sec-ch-ua-bitness
 *  https://web.dev/user-agent-client-hints/
 *  https://caniuse.com/mdn-http_headers_sec-ch-ua-bitness
 *
 * Class Bitness
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/main/LICENSE)
 *
 * @package DevCoding\Hints
 */
class Bitness extends Hint implements ConstantAwareInterface
{
  const HEADER  = 'Sec-CH-UA-Bitness';
  const DEFAULT = '32';
  const DRAFT   = true;
  const STATIC  = true;
  const VENDOR  = false;
}
