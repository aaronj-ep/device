<?php

namespace DevCoding\ValueObject\Internet\Browser;

use DevCoding\ValueObject\Internet\UserAgent;

class Generic extends BrowserAbstract
{
  const PATTERN_MATCH   = "#\b([^\/]+)/(\d+)\.(\d+)\.([^\s]+)#";
  const PATTERN_EXCLUDE = null;

  /**
   * Factory method to create a Browser object from a user agent string.
   *
   * @param string $ua
   *
   * @return bool|$this
   */
  public static function fromUserAgent($ua)
  {
    $Browser = new static();
    $ua      = ($ua instanceof UserAgent) ? $ua : new UserAgent($ua);
    if ($matches = $ua->getMatches($Browser::PATTERN_MATCH, $Browser::PATTERN_EXCLUDE))
    {
      return $Browser->setName($matches[1])->setVersion([$matches[2], $matches[3], $matches[4]]);
    }

    return false;
  }

  public function getFeatureDefaults()
  {
    return [];
  }
}
