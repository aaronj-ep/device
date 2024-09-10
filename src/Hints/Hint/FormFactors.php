<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Client\Resolver\Platform\MacOsMatcher;
use DevCoding\Client\Resolver\Platform\TvOsMatcher;
use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Hints\Base\BooleanValueInterface;
use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;
use DevCoding\Hints\Base\ListValueInterface;

/**
 * Returns the value for the Sec-UA-Form-Factors secure client hint header, or polyfills the same. This indicates the
 * user's preference for contrast.
 *
 * References:
 *   hhttps://wicg.github.io/ua-client-hints/#sec-ch-ua-form-factors
 *  https://github.com/WICG/ua-client-hints/blob/main/README.md
 *
 * Class FormFactors
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/main/LICENSE)
 *
 * @package DevCoding\Hints
 */
class FormFactors extends Hint implements ConstantAwareInterface, ListValueInterface
{
  // Value Constants
  const DESKTOP    = 'Desktop';
  const AUTOMOTIVE = 'Automotive';
  const MOBILE     = 'Mobile';
  const TABLET     = 'Tablet';
  const TV         = 'TV';
  const XR         = 'XR';
  const EINK       = 'EInk';
  const WATCH      = 'Watch';

  // Config Constants
  const HEADER  = 'Sec-CH-UA-Form-Factors';
  const DEFAULT = self::DESKTOP;
  const DRAFT   = true;
  const STATIC  = true;
  const VENDOR  = false;

  public function header(HeaderBag $HeaderBag, $additional = [])
  {
    $value = parent::header($HeaderBag, $additional);
    if (isset($value))
    {
      return $value;
    }

    // If it's mobile, it's mobile
    $mobile = $HeaderBag->resolve(Mobile::HEADER);
    if (BooleanValueInterface::TRUE === $mobile)
    {
      return self::MOBILE;
    }

    // We can guess based on a few select platforms
    $platform = $HeaderBag->resolve(Platform::HEADER);
    switch($platform)
    {
      case 'iPadOS':
        return self::TABLET;
      case 'WatchOS':
        return self::WATCH;
      case MacOsMatcher::PLATFORM:
      case 'OS X':
        return self::DESKTOP;
      case TvOsMatcher::PLATFORM:
        return self::TV;
      default:
        break;
    }

    // Obvious model matches
    if ($model = $HeaderBag->resolve(Model::HEADER))
    {
      if (preg_match('#(Pad|Tablet)#i', $model))
      {
        return self::TABLET;
      }
      elseif (preg_match('#(Phone|iPod Touch)#i', $model))
      {
        return self::MOBILE;
      }
    }

    // Classic Stuff Here
    if ($userAgent = $HeaderBag->resolve(LegacyUserAgent::HEADER))
    {
      if (preg_match('#(iPad|Touch|Tablet)#i', $userAgent) && !preg_match('#(iPod|iPhone)#i', $userAgent))
      {
        return self::TABLET;
      }
      elseif (preg_match('#(iPod Touch|iPhone|Phone|BlackBerry)#i', $userAgent))
      {
        // This is after the previous so that we don't have to do another match to exclude 'Blackberry Tablet OS'
        return self::MOBILE;
      }
      elseif (preg_match('#(Watch)#i', $userAgent))
      {
        return self::WATCH;
      }
      elseif (preg_match('#(TV|Roku|PlayStation|XBox|Nintendo|)#i', $userAgent))
      {
        return self::TV;
      }
    }

    return null;
  }
}
