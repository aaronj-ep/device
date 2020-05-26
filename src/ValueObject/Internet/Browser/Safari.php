<?php

namespace DevCoding\ValueObject\Internet\Browser;

use DevCoding\Device\Hints;
use DevCoding\Device\HintsWithFallback;
use DevCoding\ValueObject\Internet\UserAgent;

class Safari extends BrowserAbstract
{
  const NAME                 = 'Safari';
  const PATTERN_MATCH        = "#(Version)\/(\d+)\.(\d+)(?:\.(\d+))?.*Safari\/#";
  const PATTERN_MATCH_ALT    = "#(Safari)\/\d+#";
  const PATTERN_MATCH_MOBILE = "#(CriOS|EdgiOS|FxiOS)\/([0-9\.]+)#";
  const PATTERN_EXCLUDE      = '#(PhantomJS|Silk|rekonq|OPR|Chrome|Android|Edge|bot)#';

  public static function fromUserAgent($ua)
  {
    $Safari = new static();
    $ua     = ($ua instanceof UserAgent) ? $ua : new UserAgent($ua);
    if ($matches = $ua->getMatches($Safari::PATTERN_MATCH, $Safari::PATTERN_EXCLUDE))
    {
      // Safari Newer Versions
      $major = (isset($matches[2])) ? $matches[2] : null;
      $minor = (isset($matches[3])) ? $matches[3] : null;
      $rev   = (isset($matches[4])) ? $matches[4] : null;

      // Mobile Device?
      $isMobile = $ua->isMatch(HintsWithFallback::REGEX_IDEVICE);

      return $Safari->setName(self::NAME)->setVersion([$major, $minor, $rev])->setMobile($isMobile ?: false);
    }
    elseif ($matches = $ua->getMatches($Safari::PATTERN_MATCH_MOBILE, $Safari::PATTERN_EXCLUDE))
    {
      // Firefox, Edge, or Chrome on iOS
      $parts = explode('.', $matches[2]);
      $major = array_shift($parts);
      $minor = array_shift($parts);
      $rev   = implode('.', $parts);

      return $Safari->setName(self::NAME)->setVersion([$major, $minor, $rev])->setMobile(true);
    }
    else
    {
      // Versions below 3.x doe not have the Version/ in the UA.  Since we can't get the version number, we go with the
      // highest version that didn't have the version number in the UA.  It doesn't really matter, since this would be
      // a fairly feature-less browser by modern standards anyway.
      if ($matches = $ua->getMatches($Safari::PATTERN_MATCH_ALT, $Safari::PATTERN_EXCLUDE))
      {
        return $Safari->setName(self::NAME)->setVersion([2, 0, 4]);
      }
    }

    return false;
  }

  public function getFeatureDefaults()
  {
    return [
        'desktop' => [
            Hints::KEY_ARRAY_INCLUDES => 9,
            Hints::KEY_DISPLAY_FLEX   => 6.1,
            Hints::KEY_DISPLAY_GRID   => 10.1,
            Hints::KEY_LOADING        => false,
            Hints::KEY_PROMISE        => 7.1,
            Hints::KEY_SRCSET         => 9,
        ],
        'mobile' => [
          Hints::KEY_ARRAY_INCLUDES => 9,
          Hints::KEY_DISPLAY_FLEX   => 7,
          Hints::KEY_DISPLAY_GRID   => 10.3,
          Hints::KEY_LOADING        => false,
          Hints::KEY_PROMISE        => 8,
          Hints::KEY_SRCSET         => 9,
        ],
    ];
  }
}
