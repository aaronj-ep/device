<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Client\Object\Browser\BaseBrowser;
use DevCoding\Helper\Resolver\BrowserResolver;

interface BrowserResolverAwareInterface
{
  /**
   * @return BaseBrowser
   */
  public function getBrowserObject();

  /**
   * @param BrowserResolver $CookieBag
   *
   * @return BrowserResolverAwareInterface
   */
  public function setBrowserResolver(BrowserResolver $CookieBag);
}