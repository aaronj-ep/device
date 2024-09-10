<?php

namespace DevCoding\Hints\Base;

use DevCoding\Client\Object\Browser\Browser;

trait BrowserHintTrait
{
  abstract protected function config(): HintConfig;

  public function browser(Browser $Browser)
  {
    return $Browser->isSupported($this->config()->key);
  }
}
