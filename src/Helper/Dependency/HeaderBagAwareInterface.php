<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Helper\Resolver\HeaderBag;

interface HeaderBagAwareInterface
{
  /**
   * @param array|string $key_or_keys
   *
   * @return string|float|int|bool|null
   */
  public function header($key_or_keys);

  /**
   * @param HeaderBag $HeaderResolver
   *
   * @return HeaderBagAwareInterface
   */
  public function setHeaderBag(HeaderBag $HeaderResolver);
}
