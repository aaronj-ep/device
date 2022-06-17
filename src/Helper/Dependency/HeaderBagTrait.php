<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Helper\Resolver\HeaderBag;

trait HeaderBagTrait
{
  /** @var HeaderBag */
  protected $_HeaderBag;

  /**
   * Returns the first header from the given array of headers that is set in the $_SERVER array.
   *
   * @param array|string $key_or_keys
   *
   * @return mixed|null
   */
  public function header($key_or_keys)
  {
    return $this->getHeaderBag()->resolve($key_or_keys);
  }

  /**
   * Extends the 'header' method to normalize 'no-preference' to a FALSE response, and 'REDUCE' to a TRUE response.
   *
   * @param array|string $key_or_keys
   *
   * @return bool|string|null
   */
  public function prefers($key_or_keys)
  {
    $value = $this->header($key_or_keys);
    if (isset($value))
    {
      if ('no-preference' === $value)
      {
        return false;
      }
      elseif ('reduce' === $value)
      {
        return true;
      }

      return $value;
    }

    return null;
  }

  /**
   * @param HeaderBag $HeaderBag
   *
   * @return $this
   */
  public function setHeaderBag(HeaderBag $HeaderBag)
  {
    $this->_HeaderBag = $HeaderBag;

    return $this;
  }

  protected function getHeaderBag()
  {
    if (!isset($this->_HeaderBag))
    {
      $this->_HeaderBag = new HeaderBag();
    }

    return $this->_HeaderBag;
  }
}
