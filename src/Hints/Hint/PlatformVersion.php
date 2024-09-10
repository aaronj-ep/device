<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Client\Factory\PlatformFactory;
use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;
use DevCoding\Helper\Resolver\HeaderBag;

/**
 * Returns the value for the Sec-CH-UA-Platform-Version client hint header, or polyfills the same. This is intended to
 * indicate the version of the os/platform of the device.
 *
 * Note that for Linux, this value is always empty. For Windows, the return value does not return the values you might
 * think. Read the references below for more information.
 *
 * References:
 *   https://wicg.github.io/ua-client-hints/#sec-ch-ua-platform-version
 *   https://web.dev/user-agent-client-hints/
 *   https://docs.microsoft.com/en-us/microsoft-edge/web-platform/how-to-detect-win11
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
class PlatformVersion extends Hint implements ConstantAwareInterface
{
  const HEADER  = 'Sec-CH-UA-Platform-Version';
  const DEFAULT = '1.0.0';
  const DRAFT   = false;
  const STATIC  = true;
  const VENDOR  = false;

  public function header(HeaderBag $HeaderBag, $additional = [])
  {
    if (!$header = parent::header($HeaderBag, $additional))
    {
      if ($legacy = (new LegacyUserAgent())->header($HeaderBag))
      {
        $object = (new PlatformFactory())->fromString($legacy);
        $pfName = $object->getName() ?? 'Unknown';

        if ('Linux' === $pfName)
        {
          // All Linux should return an empty string.
          // https://wicg.github.io/ua-client-hints/#sec-ch-ua-platform-version
          $header = '';
        }
        elseif ('iOS' === $pfName || 'Android' === $pfName)
        {
          // Android & iOS should return their versions
          // https://wicg.github.io/ua-client-hints/#sec-ch-ua-platform-version
          $header = (string) $object->getVersion();
        }
        elseif ('Windows' === $pfName)
        {
          // Windows is a little weird...
          // https://docs.microsoft.com/en-us/microsoft-edge/web-platform/how-to-detect-win11
          $major = $object->getVersion()->getMajor();
          if ($major < 10)
          {
            $header = '0';
          }
          else
          {
            $header = ($major >= 11) ? '13' : '8';
          }
        }
        else
        {
          // macOS and other versions should return between 1 and 3 parts
          // https://wicg.github.io/ua-client-hints/#sec-ch-ua-platform-version
          $Version = $object->getVersion();
          $parts   = array_filter([$Version->getMajor(), $Version->getMinor(), $Version->getPatch()]);

          $header = implode('.', $parts);
        }
      }
    }

    return $header;
  }
}
