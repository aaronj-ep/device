<?php

namespace DevCoding\Hints\Base;

use DevCoding\Helper\Resolver\HeaderBag;

trait HeaderHintTrait
{
  abstract protected function config(): HintConfig;

  public function header(HeaderBag $HeaderBag, $additional = [])
  {
    return $HeaderBag->resolve(array_merge($this->config()->headers(), $additional));
  }
}
