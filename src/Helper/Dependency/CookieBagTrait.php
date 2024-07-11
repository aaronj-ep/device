<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Helper\Resolver\CookieBag;

trait CookieBagTrait
{
  /** @var CookieBag */
  protected $_CookieBag;

  /**
   * @param string|null $key
   *
   * @return mixed|null
   */
  public function cookie($key)
  {
    return $key ? $this->getCookieBag()->resolve($key) : null;
  }

  /**
   * @param CookieBag $CookieBag
   *
   * @return $this
   */
  public function setCookieBag(CookieBag $CookieBag)
  {
    $this->_CookieBag = $CookieBag;

    return $this;
  }

  /**
   * @return CookieBag
   */
  protected function getCookieBag()
  {
    if (!isset($this->_CookieBag))
    {
      $this->_CookieBag = new CookieBag();
    }

    return $this->_CookieBag;
  }
}
