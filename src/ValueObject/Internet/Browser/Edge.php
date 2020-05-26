<?php

namespace DevCoding\ValueObject\Internet\Browser;

use DevCoding\Device\Hints;
use DevCoding\ValueObject\Internet\UserAgent;

class Edge extends BrowserAbstract
{
  const NAME          = 'Edge';
  const PATTERN_MATCH = "#(Edge)\/([0-9_.]+)#";
  // Nothing is known to try to look like a faux Edge, but we'll still exclude 'bots'
  const PATTERN_EXCLUDE = '#bot#i';

  public static function fromUserAgent($ua)
  {
    $Edge = new static();
    $ua   = ($ua instanceof UserAgent) ? $ua : new UserAgent($ua);
    if ($matches = $ua->getMatches($Edge::PATTERN_MATCH, $Edge::PATTERN_EXCLUDE))
    {
      $isMobile = $ua->isMatch('#(Windows Mobile)#');

      return $Edge->setName(self::NAME)
                  ->setMobile($isMobile ?: null)
                  ->setVersion($matches[2])
      ;
    }

    return false;
  }

  public function getFeatureDefaults()
  {
    return [
      Hints::KEY_ARRAY_INCLUDES => 14,
      Hints::KEY_DISPLAY_FLEX   => 12,
      Hints::KEY_DISPLAY_GRID   => 12,
      // No version supports native lazy loading
      Hints::KEY_LOADING => false,
      Hints::KEY_PROMISE => 12,
      // v13-15 display distored images for SRCSET, v12 only has partial support.
      Hints::KEY_SRCSET => 16,
    ];
  }
}
