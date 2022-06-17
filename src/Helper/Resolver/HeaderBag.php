<?php

namespace DevCoding\Helper\Resolver;

use DevCoding\Hints\ClientHints;
use DevCoding\Client\Object\Headers\HeaderBag as BaseHeaderBag;

class HeaderBag extends BaseHeaderBag
{
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
