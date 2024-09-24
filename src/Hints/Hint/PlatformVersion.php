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
          $winVer = $object->getVersion();
          $major  = $winVer->getMajor();
          $minor  = $winVer->getMinor();

          if ($major < 7)
          {
            return '0.0.0';
          }
          elseif (7 === $major)
          {
            return '0.1.0';
          }
          elseif (8 === $major && 0 === $minor)
          {
            return '0.2.0';
          }
          elseif (8 === $major && $minor >= 1)
          {
            return '0.3.0';
          }
          elseif($major < 11)
          {
            // This should really only be Windows 10, but we'll account for other possiblities
            return sprintf('%s.%s', $major, $minor);
          }
          else
          {
            // Will return 13.0 for Windows 11, and extrapolate later versions (likely incorrectly).
            return sprintf('%s.%s', $major + 2, 0);
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
