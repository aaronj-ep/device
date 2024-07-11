<?php

namespace DevCoding\Helper\Resolver;

use DevCoding\Client\Factory\BrowserFactory;
use DevCoding\Client\Object\Browser\BaseBrowser;
use DevCoding\Client\Object\Browser\Browser;
use DevCoding\Client\Object\Browser\BrowserImmutable;
use DevCoding\Client\Object\Headers\UserAgentString;
use DevCoding\Helper\Dependency\ConfigBagAwareInterface;
use DevCoding\Helper\Dependency\ConfigBagTrait;
use DevCoding\Helper\Dependency\HeaderBagAwareInterface;
use DevCoding\Helper\Dependency\HeaderBagTrait;
use DevCoding\Hints\Hint\FullVersionList;
use DevCoding\Hints\Hint\UserAgent;

class BrowserResolver implements HeaderBagAwareInterface, ConfigBagAwareInterface
{
  use HeaderBagTrait;
  use ConfigBagTrait;

  /** @var BaseBrowser */
  protected $_Browser;

  /**
   * @return Browser|false
   */
  public function getBrowser()
  {
    if (!isset($this->_Browser))
    {
      $header  = $this->getHeader();
      $factory = BrowserFactory::fromConfig($this->getConfigBag()->getBrowsers());

      $this->_Browser = $factory->build($header) ?? false;
    }

    return $this->_Browser;
  }

  private function getHeader()
  {
    $headers = array(UserAgent::KEY, FullVersionList::KEY);
    foreach (UserAgentString::HEADERS as $header)
    {
      $headers[] = str_replace('HTTP_', '', $header);
    }

    return $this->getHeaderBag()->resolve($headers);
  }
}