<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Client\Factory\PlatformFactory;
use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;

/**
 * Returns the value for the Sec-CH-UA-Platform client hint header, or polyfills the same. This is intended to indicate
 * the platform or operating system of the device.
 *
 * References:
 *   https://wicg.github.io/ua-client-hints/#sec-ch-ua-platform
 *   https://web.dev/user-agent-client-hints/
 *
 * Class Platform
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class Platform extends Hint implements ConstantAwareInterface
{
  const HEADER  = 'Sec-CH-UA-Platform';
  const DEFAULT = 'Unknown';
  const DRAFT   = false;
  const STATIC  = true;
  const VENDOR  = false;

  public function header(HeaderBag $HeaderBag, $additional = [])
  {
    if (!$value = parent::header($HeaderBag, $additional))
    {
      if ($legacy = (new LegacyUserAgent())->header($HeaderBag))
      {
        $value = (new PlatformFactory())->fromString($legacy)->getName();
      }
    }

    return $value;
  }
}
