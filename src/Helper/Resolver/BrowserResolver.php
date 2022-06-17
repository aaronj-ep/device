<?php

namespace DevCoding\Helper\Resolver;

use DevCoding\Client\Factory\BrowserFactory;
use DevCoding\Client\Object\Browser\BaseBrowser;
use DevCoding\Client\Object\Browser\BrowserImmutable;

class BrowserResolver
{
  /** @var BaseBrowser */
  protected $_Browser;

  /**
   * @return BrowserImmutable|null
   */
  public function getBrowser()
  {
    if (empty($this->_Browser))
    {
      $this->_Browser = (new BrowserFactory())->fromHeaders();
    }

    return $this->_Browser;
  }
}