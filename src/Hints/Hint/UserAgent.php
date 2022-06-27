<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Client\Factory\BrowserFactory;
use DevCoding\Client\Object\Browser\BaseBrowser;
use DevCoding\Helper\Dependency\BrowserResolverAwareInterface;
use DevCoding\Helper\Dependency\BrowserResolverTrait;
use DevCoding\Hints\Base\GreaseTrait;
use DevCoding\Hints\Base\HeaderBagHint;

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
class UserAgent extends HeaderBagHint implements BrowserResolverAwareInterface
{
  use GreaseTrait;
  use BrowserResolverTrait;

  const KEY             = 'Sec-CH-UA';
  const DEFAULT_VERSION = 73;

  /**
   * @return string
   */
  public function get()
  {
    $header = $this->header(self::KEY);

    if (!isset($header))
    {
      if ($Browser = $this->getObject())
      {
        $header = sprintf(
            '"%s"; v="%s", %s, "%s"; v="%s"',
            $Browser->getBrand(),
            $Browser->getVersion()->getMajor(),
            $this->getGrease(false, self::DEFAULT_VERSION),
            $Browser->getEngine(),
            $Browser->getVersion()->getMajor()
        );
      }
    }

    return $header ?? $this->getDefault();
  }

  /**
   * @return string
   */
  public function getDefault()
  {
    return $this->getBrands(false, self::DEFAULT_VERSION);
  }

  /**
   * @return bool
   */
  public function isNative()
  {
    return true;
  }

  /**
   * @return bool
   */
  public function isVendor()
  {
    return false;
  }

  /**
   * @return bool
   */
  public function isDraft()
  {
    return false;
  }

  /**
   * @return bool
   */
  public function isStatic()
  {
    return true;
  }

  /**
   * @return BaseBrowser|null
   */
  public function getObject()
  {
    return $this->getBrowserObject();
  }
}
