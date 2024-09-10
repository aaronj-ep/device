<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;

/**
 * Returns the value for the Device-Memory client hint header, or polyfills the same. This indicates the amount of
 * device RAM. The amount of device RAM can be used as a fingerprinting variable, so values for the header are
 * intentionally coarse to reduce the potential for its misuse.
 *
 * References:
 *  https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Device-Memory
 *  https://caniuse.com/mdn-http_headers_device-memory
 *
 * Class DeviceMemory
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/main/LICENSE)
 *
 * @package DevCoding\Hints
 */
class DeviceMemory extends Hint implements ConstantAwareInterface
{
  const HEADER  = 'Device-Memory';
  const DEFAULT = '4';
  const DRAFT   = false;
  const STATIC  = true;
  const VENDOR  = false;
}
