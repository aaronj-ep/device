<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Client\Object\Browser\Browser;

interface BrowserResolverAwareInterface
{
  /**
   * @return Browser
   */
  public function getBrowserObject();

  /**
   * @param BrowserResolver $BrowserResolver
   *
   * @return BrowserResolverAwareInterface
   */
  public function setBrowserResolver(BrowserResolver $BrowserResolver): BrowserResolverAwareInterface;
}