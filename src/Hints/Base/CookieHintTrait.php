<?php

namespace DevCoding\Hints\Base;

use DevCoding\Helper\Resolver\CookieBag;

/**
 * Trait to use in classes that extend Hint and can have their value stored in a cookie.
 */
trait CookieHintTrait
{
  /**
   * Must return this object's configuration as a HintConfig object.
   *
   * @return HintConfig
   */
  abstract protected function config(): HintConfig;

  /**
   * Attempts to resolve the value of the object using this trait by retrieving the value from the given CookieBag.
   *
   * @param CookieBag $CookieBag
   *
   * @return mixed|null
   */
  public function cookie(CookieBag $CookieBag)
  {
    $cookies = is_array($this->config()->cookie) ? $this->config()->cookies : [$this->config()->cookie];

    return $CookieBag->resolve($cookies);
  }
}
