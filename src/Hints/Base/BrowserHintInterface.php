<?php

namespace DevCoding\Hints\Base;

use DevCoding\Client\Object\Browser\Browser;

interface BrowserHintInterface
{
  /**
   * @param Browser $Browser
   *
   * @return string|float|int|bool|null
   */
  public function browser(Browser $Browser);
}
