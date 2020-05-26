<?php

namespace DevCoding\ValueObject\Internet\Browser;

use DevCoding\Device\Hints;
use DevCoding\ValueObject\Internet\UserAgent;

class Chrome extends BrowserAbstract
{
  const NAME = 'Chrome';
  // For our purposes, Chrome and Chromium are the same.
  const PATTERN_MATCH = "#(Chrome|Chromium)\/([0-9\.]+)#";
  // These things go around acting like they are chrome, but they aren't.
  const PATTERN_EXCLUDE = '#(MRCHROME|FlyFlow|baidubrowser|bot|Edge|Edg|Silk|MxBrowser|Crosswalk|Slack_SSB|HipChat|IEMobile)#i';

  /**
   * @param string $ua
   *
   * @return Chrome|false
   */
  public static function fromUserAgent($ua)
  {
    $Chrome = new static();
    $ua     = ($ua instanceof UserAgent) ? $ua : new UserAgent($ua);

    if ($matches = $ua->getMatches($Chrome::PATTERN_MATCH, $Chrome::PATTERN_EXCLUDE))
    {
      $parts  = explode('.', $matches[2]);
      $major  = array_shift($parts);
      $minor  = array_shift($parts);
      $rev    = implode('.', $parts);
      $mobile = $ua->isMatch('#(CrMo|EdgA|Android|Mobile)#i');

      return $Chrome->setName($Chrome::NAME)->setVersion([$major, $minor, $rev])->setMobile($mobile);
    }

    return false;
  }

  public function getFeatureDefaults()
  {
    return [
      // No distinction between mobile and desktop for feature version releases it seems
      Hints::KEY_ARRAY_INCLUDES => 47,
      Hints::KEY_DISPLAY_FLEX   => 21,
      Hints::KEY_DISPLAY_GRID   => 57,
      Hints::KEY_LOADING        => 76,
      Hints::KEY_PROMISE        => 33,
      Hints::KEY_SRCSET         => 38,
    ];
  }
}
