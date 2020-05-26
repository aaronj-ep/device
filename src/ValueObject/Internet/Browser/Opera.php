<?php

namespace DevCoding\ValueObject\Internet\Browser;

use DevCoding\Device\Hints;
use DevCoding\ValueObject\Internet\UserAgent;

class Opera extends BrowserAbstract
{
  const NAME = 'Opera';

  protected $isMini = false;

  public static function fromUserAgent($ua)
  {
    $Opera = new static();
    $ua    = ($ua instanceof UserAgent) ? $ua : new UserAgent($ua);

    // The 'Opera/x.xx' stopped at 9.80, then the version went into the 'Version/x.x.x' format.
    if ($m = $ua->getMatches("#(Opera)\/9.80.*Version\/((\d+)\.(\d+)(?:\.(\d+))?)#"))
    {
      $Opera->setName(self::NAME)->setVersion([$m[3], $m[4], null]);
    }
    // Opera version 15 was a freak UA, looking like Chrome, but with OPR in the string.
    elseif ($m = $ua->getMatches("#(?:Chrome).*(OPR)\/(\d+)\.(\d+)\.(\d+)#"))
    {
      $Opera->setName(self::NAME)->setVersion([$m[3], $m[4], null]);
    }
    elseif ($m = $ua->getMatches("#(?:Mobile Safari).*(OPR)\/(\d+)\.(\d+)\.(\d+)#"))
    {
      $Opera->setName(self::NAME)->setVersion([$m[3], $m[4], null]);
    }
    elseif ($m = $ua->getMatches("#Opera (([0-9]+)\.?([0-9]*))#"))
    {
      $Opera->setName(self::NAME)->setVersion([$m[2], $m[3], null]);
    }

    if ($Opera instanceof Opera)
    {
      $rUa = (!empty($_SERVER['Device-Stock-UA'])) ? $_SERVER['Device-Stock-UA'] : null;
      if ($rUa)
      {
        $RealUserAgent = new UserAgent($rUa);
        $isMiniHeader  = isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']);
        if ($isMiniHeader || $RealUserAgent->isMatch('#(mobi)#i'))
        {
          $Opera->setMini(true);
        }
        elseif ($RealUserAgent->isMatch('#(mobi)#i'))
        {
          $Opera->setMobile(true);
        }
      }

      return $Opera;
    }

    return false;
  }

  public function getFeatureDefaults()
  {
    if ($this->isMini())
    {
      return [];
    }
    else
    {
      return [
          'desktop' => [
              Hints::KEY_ARRAY_INCLUDES => 34,
              Hints::KEY_DISPLAY_FLEX   => 12.1,
              Hints::KEY_DISPLAY_GRID   => 44,
              Hints::KEY_LOADING        => 64,
              Hints::KEY_PROMISE        => 7.1,
              Hints::KEY_SRCSET         => 25,
          ],
          'mobile' => [
              Hints::KEY_ARRAY_INCLUDES => 12.2,
              Hints::KEY_DISPLAY_FLEX   => 12.1,
              Hints::KEY_DISPLAY_GRID   => 12.2,
              Hints::KEY_LOADING        => false,
              Hints::KEY_PROMISE        => 12.2,
              Hints::KEY_SRCSET         => 12.2,
          ],
      ];
    }
  }

  public function isMobile()
  {
    $this->isMini() || parent::isMobile();
  }

  public function isMini()
  {
    return $this->isMini;
  }

  public function setMini($mini)
  {
    $this->isMini = $mini;

    return $this;
  }
}
