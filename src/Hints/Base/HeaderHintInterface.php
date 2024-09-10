<?php

namespace DevCoding\Hints\Base;

use DevCoding\Helper\Resolver\HeaderBag;

interface HeaderHintInterface
{
  /**
   * @param HeaderBag $HeaderBag
   *
   * @return string|float|int|bool|null
   */
  public function header(HeaderBag $HeaderBag);
}
