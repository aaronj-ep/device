<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;

/**
 * Returns the value for the DPR client hint header, or polyfills the same.  This indicates the device pixel ratio.
 *
 * References:
 *   https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/DPR
 *   https://developers.google.com/web/fundamentals/performance/optimizing-content-efficiency/client-hints#device_hints
 *   https://caniuse.com/client-hints-dpr-width-viewport
 *
 * Class DPR
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/main/LICENSE)
 *
 * @package DevCoding\Hints
 */
class DPR extends Hint implements ConstantAwareInterface
{
  const HEADER     = 'Sec-CH-DPR';
  const ALTERNATES = ['DPR'];
  const DEFAULT    = 1;
  const DRAFT      = false;
  const STATIC     = false;
  const VENDOR     = false;
}
