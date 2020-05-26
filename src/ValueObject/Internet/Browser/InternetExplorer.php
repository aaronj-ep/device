<?php

namespace DevCoding\ValueObject\Internet\Browser;

use DevCoding\Device\Hints;
use DevCoding\ValueObject\Internet\UserAgent;

class InternetExplorer extends BrowserAbstract
{
  const NAME              = 'Internet Explorer';
  const PATTERN_MATCH     = '#(Trident)\/[0-9]\.[0-9];[^rv]*rv:(([0-9._]+))#';
  const PATTERN_MATCH_ALT = "#(MSIE)\s*([0-9_.]+)[^;]*;#";
  const PATTERN_MOBILE    = '#(Windows Phone|IEMobile|MSIEMobile|Windows CE)#i';
  const PATTERN_EXCLUDE   = '#(bot)#i';

  public static function fromUserAgent($ua)
  {
    $IE = new static();
    $ua = ($ua instanceof UserAgent) ? $ua : new UserAgent($ua);
    if (!$matches = $ua->getMatches($IE::PATTERN_MATCH, $IE::PATTERN_EXCLUDE))
    {
      $matches = $ua->getMatches($IE::PATTERN_MATCH_ALT, $IE::PATTERN_EXCLUDE);
    }

    if ($matches)
    {
      $isMobile = $ua->isMatch(self::PATTERN_MOBILE);
      return $IE
          ->setName(self::NAME)
          ->setMobile($isMobile ?: null)
          ->setVersion($matches[2])
      ;
    }

    return false;
  }

  public function getFeatureDefaults()
  {
    return [
        Hints::KEY_ARRAY_INCLUDES => false,
        Hints::KEY_DISPLAY_FLEX   => 11,
        Hints::KEY_DISPLAY_GRID   => false,
        Hints::KEY_LOADING        => false,
        Hints::KEY_PROMISE        => false,
        Hints::KEY_SRCSET         => false,
    ];
  }
}
