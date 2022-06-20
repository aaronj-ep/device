<?php

namespace DevCoding\Hints\Hint;

use DevCoding\CodeObject\Object\Base\BaseVersion;
use DevCoding\Helper\Dependency\BrowserResolverAwareInterface;
use DevCoding\Helper\Dependency\BrowserResolverTrait;
use DevCoding\Hints\Base\GreaseTrait;
use DevCoding\Hints\Base\HeaderBagHint;
use DevCoding\Client\Object\Headers\HeaderBag;

/**
 * Returns the value for the Sec-CH-UA-Full-Version-List client hint header, or polyfills the same. This is intended to
 * indicate the version of the browser on the device.
 *
 * References:
 *   https://wicg.github.io/ua-client-hints/#sec-ch-ua-full-version-list
 *   https://web.dev/user-agent-client-hints/
 *
 * Class FullVersionList
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Hints
 */
class FullVersionList extends HeaderBagHint implements BrowserResolverAwareInterface
{
  use BrowserResolverTrait;
  use GreaseTrait;

  const KEY = 'Sec-CH-UA-Full-Version-List';

  /**
   * @return string
   */
  public function get()
  {
    $header = $this->header(self::KEY);

    if (!isset($header))
    {
      if ($version = $this->getObject())
      {
        $header  = sprintf(
            '"%s"; v="%s", %s, "%s"; v="%s"',
            $this->getBrowserObject()->getBrand(),
            $version,
            $this->getGrease(false, UserAgent::DEFAULT_VERSION),
            $this->getBrowserObject()->getEngine(),
            $version
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
    return $this->getBrands(true, UserAgent::DEFAULT_VERSION);
  }

  /**
   * @return BaseVersion|null
   */
  public function getObject()
  {
    return ($Browser = $this->getBrowserObject()) ? $Browser->getVersion() : null;
  }

  /**
   * @return bool
   */
  public function isDraft()
  {
    return true;
  }

  /**
   * @return bool
   */
  public function isNative()
  {
    return true;
  }

  public function isStatic()
  {
    return true;
  }

  /**
   * @return false
   */
  public function isVendor()
  {
    return false;
  }

  /**
   * @return HeaderBag
   */
  protected function getHeaderBag()
  {
    return $this->_HeaderBag;
  }
}
