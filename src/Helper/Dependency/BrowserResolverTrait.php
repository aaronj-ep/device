<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Client\Object\Browser\BaseBrowser;
use DevCoding\Client\Object\Browser\Browser;
use DevCoding\Helper\Resolver\BrowserResolver;

trait BrowserResolverTrait
{
  /** @var BrowserResolver */
  protected $_BrowserResolver;

  /**
   * @return Browser|null
   */
  public function getBrowserObject()
  {
    return $this->_BrowserResolver->getBrowser();
  }

  public function setBrowserResolver(BrowserResolver $BrowserBag): BrowserResolverAwareInterface
  {
    $this->_BrowserResolver = $BrowserBag;

    return $this;
  }
}