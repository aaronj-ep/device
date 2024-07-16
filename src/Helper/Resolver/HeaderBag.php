<?php

namespace DevCoding\Helper\Resolver;

use DevCoding\Hints\ClientHints;
use DevCoding\Client\Object\Headers\HeaderBag as BaseHeaderBag;

class HeaderBag extends BaseHeaderBag
{
  public function get(string $id)
  {
    if ($value = parent::get($id))
    {
      return preg_replace('#(?:"([^,]*)")#', '$1', $value);
    }

    return null;
  }

  /**
   * Strips Sec-CH- or CH- from the given header name, providing a consistent way to key hints
   *
   * @param string $name
   *
   * @return string
   */
  public static function key(string $name): string
  {
    return preg_replace('#^Sec-CH-|CH-#i', '', $name);
  }

  /**
   * @return bool
   */
  public function isHinted()
  {
    $headers = explode(',', ClientHints::getenv('CLIENT_HINTS'));

    foreach ($headers as $header)
    {
      if (!$this->has(trim($header)))
      {
        return false;
      }
    }

    return true;
  }
}
