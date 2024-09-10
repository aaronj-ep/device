<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Hints\Base\ConstantAwareInterface;
use DevCoding\Hints\Base\Hint;
use DevCoding\Client\Object\Headers\UserAgentString as UserAgentObject;

/**
 * Returns the value for the USER_AGENT header, or polyfills the same using other headers likely to contain the user
 * agent string.
 *
 * Class LegacyUserAgent
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class LegacyUserAgent extends Hint implements ConstantAwareInterface
{
  const HEADER  = 'User-Agent';
  const DEFAULT = 'Mozilla/5.0 (Unknown) Unknown (Unknown)';
  const DRAFT   = false;
  const STATIC  = true;
  const VENDOR  = false;

  public function header(HeaderBag $HeaderBag, $additional = [])
  {
    foreach (UserAgentObject::HEADERS as $header)
    {
      $additional[] = str_replace('HTTP_', '', $header);
    }

    return parent::header($HeaderBag, $additional);
  }
}
