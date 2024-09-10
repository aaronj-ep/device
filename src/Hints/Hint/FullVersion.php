<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Client\Object\Browser\Browser;
use DevCoding\Client\Object\Headers\UAFullVersionList;
use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Hints\Base\BrowserHintInterface;
use DevCoding\Hints\Base\BrowserHintTrait;
use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;

/**
 * Returns the value for the Sec-CH-UA-Full-Version-List client hint header, or polyfills the same. This is intended to
 * indicate the version of the browser on the device.
 *
 * References:
 *   https://wicg.github.io/ua-client-hints/#sec-ch-ua-full-version
 *   https://web.dev/user-agent-client-hints/
 *
 * Class FullVersion
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class FullVersion extends Hint implements ConstantAwareInterface, BrowserHintInterface
{
  use BrowserHintTrait;

  const HEADER  = 'Sec-CH-UA-Full-Version';
  const DEFAULT = '';
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

    if ($value = $HeaderBag->resolve(FullVersionList::HEADER))
    {
      return (new UAFullVersionList($value))->getVersion()->getRaw();
    }

    return null;
  }

  public function browser(Browser $Browser)
  {
    return $Browser->getVersion()->getRaw();
  }
}
