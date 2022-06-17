<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Helper\Resolver\CookieBag;

interface CookieBagAwareInterface
{
  /**
   * @param string $key
   *
   * @return mixed
   */
  public function cookie($key);

  /**
   * @param CookieBag $CookieBag
   *
   * @return CookieBagAwareInterface
   */
  public function setCookieBag(CookieBag $CookieBag);
}
