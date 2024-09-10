<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Client\Object\Browser\BaseBrowser;
use DevCoding\Client\Object\Browser\Browser;
use DevCoding\Client\Object\Headers\UA;
use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Hints\Base\BrowserHintInterface;
use DevCoding\Hints\Base\BrowserHintTrait;
use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;

/**
 * Returns the value for the Sec-CH-UA client hint header, or polyfills the same. This is intended to indicate
 * the browser of the device.
 *
 * References:
 *   https://wicg.github.io/ua-client-hints/#sec-ch-ua
 *   https://web.dev/user-agent-client-hints/
 *
 * Class UserAgent
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class UserAgent extends Hint implements ConstantAwareInterface, BrowserHintInterface
{
  use BrowserHintTrait;

  const HEADER = 'Sec-CH-UA';
  const DEFAULT = '';
  const DRAFT  = true;
  const STATIC = true;
  const VENDOR = false;

  public function header(HeaderBag $HeaderBag, $additional = [])
  {
    if (!$value = parent::header($HeaderBag, $additional))
    {
      if ($HeaderBag->has(FullVersionList::HEADER))
      {
        // Get the full version list header instead
        $full = $HeaderBag->get(FullVersionList::HEADER);

        // Remove all but 'major' part of version.  While this isn't the same as 'significant', it's all
        // we can determine on the fly without a list of significant versions
        $object = new UA(preg_replace('#v="\s*(([0-9]+)[^"]+)"#', '$2', $full));

        $value = $object->getString();
      }
    }

    return $value;
  }

  public function browser(Browser $Browser)
  {
    return $Browser->getUserAgent()->getString();
  }
}
