<?php

namespace DevCoding\Hints\Base;

use DevCoding\Helper\Resolver\CookieBag;

/**
 * Hint classes that implement this interface may provide their values from a CookieBag.
 */
interface CookieHintInterface
{
  /**
   * @param CookieBag $CookieBag
   *
   * @return string|float|int|bool|null
   */
  public function cookie(CookieBag $CookieBag);
}
