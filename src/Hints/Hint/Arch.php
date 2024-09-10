<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;

/**
 * Returns the value for the Sec-CH-UA-Arch client hint header, or polyfills the same. This indicates the device
 * architecture's instruction set.
 *
 * References:
 *  https://wicg.github.io/ua-client-hints/#sec-ch-ua-arch
 *  https://web.dev/user-agent-client-hints/
 *  https://caniuse.com/mdn-http_headers_sec-ch-ua-arch
 *
 * Class Arch
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/main/LICENSE)
 *
 * @package DevCoding\Hints
 */
class Arch extends Hint implements ConstantAwareInterface
{
  const HEADER  = 'Sec-CH-UA-Arch';
  const DEFAULT = 'x86';
  const DRAFT   = true;
  const STATIC  = true;
  const VENDOR  = false;
}
