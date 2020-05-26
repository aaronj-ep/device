<?php

namespace DevCoding\ValueObject\Internet\Browser;

use DevCoding\Device\Hints;
use DevCoding\ValueObject\Internet\UserAgent;

class Firefox extends BrowserAbstract
{
  const NAME = 'Firefox';

  // Wow, firefox could look like ANY of these things.  Most are mobile.
  const PATTERN_MATCH = "#(Firefox|Fennec|Namoroka|Shiretoko|Minefield|MozillaDeveloperPreview)\/([^\s^;^)]+)#";
  // If it says this, it's just a faker, not really firefox.
  const PATTERN_EXCLUDE = '#(bot|MSIE|HbbTV|Chimera|Seamonkey|Camino)#i';
  // Mobile Patterns
  const PATTERN_MOBILE = '#(Fennec|Tablet|Phone|Mobile|Maemo)#i';

  public static function fromUserAgent($ua)
  {
    $Firefox = new static();
    $ua      = ($ua instanceof UserAgent) ? $ua : new UserAgent($ua);
    if ($matches = $ua->getMatches($Firefox::PATTERN_MATCH, $Firefox::PATTERN_EXCLUDE))
    {
      $isMobile = $ua->isMatch($Firefox::PATTERN_MOBILE);

      $parts = explode('.', $matches[2]);
      $major = array_shift($parts);
      $minor = array_shift($parts);
      $rev   = implode('.', $parts);

      return $Firefox->setName(self::NAME)->setVersion([$major, $minor, $rev])->setMobile($isMobile);
    }

    return false;
  }

  public function getFeatureDefaults()
  {
    return [
        'desktop' => [
            Hints::KEY_ARRAY_INCLUDES => 43,
            Hints::KEY_DISPLAY_FLEX   => 28,
            Hints::KEY_DISPLAY_GRID   => 52,
            Hints::KEY_LOADING        => 75,
            Hints::KEY_PROMISE        => 29,
            Hints::KEY_SRCSET         => 38,
        ],
        'mobile' => [
            // v68 was first real mobile version
            Hints::KEY_ARRAY_INCLUDES => 68,
            Hints::KEY_DISPLAY_FLEX   => 68,
            Hints::KEY_DISPLAY_GRID   => 68,
            Hints::KEY_LOADING        => 75,
            Hints::KEY_PROMISE        => 68,
            Hints::KEY_SRCSET         => 68,
        ],
    ];
  }
}
