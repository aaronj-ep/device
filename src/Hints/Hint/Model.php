<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;

/**
 * Returns the value for the Sec-CH-UA-Model client hint header, or polyfills the same. This indicates the device model.
 *
 * References:
 *  https://wicg.github.io/ua-client-hints/#sec-ch-ua-model
 *  https://web.dev/user-agent-client-hints/
 *
 * Class Model
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class Model extends Hint implements ConstantAwareInterface
{
  const HEADER  = 'Sec-CH-UA-Model';
  const DEFAULT = 'Unknown';
  const DRAFT   = false;
  const STATIC  = true;
  const VENDOR  = false;
}
