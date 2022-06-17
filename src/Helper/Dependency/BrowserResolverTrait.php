<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Client\Object\Browser\BaseBrowser;
use DevCoding\Helper\Resolver\BrowserResolver;

trait BrowserResolverTrait
{
  /** @var BrowserResolver */
  protected $_BrowserResolver;

  /**
   * @return BaseBrowser|null
   */
  public function getBrowserObject()
  {
    return $this->_BrowserResolver->getBrowser();
  }

  public function setBrowserResolver(BrowserResolver $BrowserBag)
  {
    $this->_BrowserResolver = $BrowserBag;

    return $this;
  }
}