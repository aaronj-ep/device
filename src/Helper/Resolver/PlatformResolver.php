<?php

namespace DevCoding\Helper\Resolver;

use DevCoding\Client\Factory\PlatformFactory;
use DevCoding\Client\Object\Headers\UserAgentString;
use DevCoding\Client\Object\Platform\PlatformImmutable as PlatformObject;
use DevCoding\Helper\Dependency\HeaderBagAwareInterface;
use DevCoding\Helper\Dependency\HeaderBagTrait;
use DevCoding\Hints\Hint\Platform;
use DevCoding\Hints\Hint\PlatformVersion;

class PlatformResolver implements HeaderBagAwareInterface
{
  use HeaderBagTrait;

  /** @var PlatformObject */
  protected $_Platform;

  /**
   * @return PlatformObject
   *
   * @throws \Exception
   */
  public function getPlatform()
  {
    $buildObject = function()
    {
      if ($name = $this->header(Platform::KEY))
      {
        if ($version = $this->header(PlatformVersion::KEY))
        {
          if ('Windows' === $name)
          {
            if ($version > 0)
            {
              if ($version >= 13)
              {
                return new PlatformObject($name, 11);
              }

              return new PlatformObject($name, 10);
            }
          }

          return new PlatformObject($name, $version);
        }
      }

      return (new PlatformFactory())->fromUserAgent(new UserAgentString($this->getHeader()));
    };

    if (!isset($this->_Platform))
    {
      $this->_Platform = $buildObject();
    }

    return $this->_Platform;
  }

  private function getHeader()
  {
    $headers = [];
    foreach (UserAgentString::HEADERS as $header)
    {
      $headers[] = str_replace('HTTP_', '', $header);
    }

    return $this->getHeaderBag()->resolve($headers);
  }
}
