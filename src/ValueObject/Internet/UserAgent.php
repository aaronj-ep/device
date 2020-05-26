<?php

namespace DevCoding\ValueObject\Internet;

class UserAgent
{
  const BOTS = [
      'crawler', 'bot', 'spider', 'archiver', 'scraper', 'stripper', 'wget', 'curl', 'AppEngine-Google',
      'AdsBot-Google', 'AdsBot-Google-Mobile-Apps', 'Mediapartners-Google', 'Slurp', 'facebookexternalhit',
      'Zeus 32297 Webster Pro', '008', 'PagePeeker', 'Nutch', 'grub-client', 'NewsGator', 'Yandex',
  ];

  /** @var string */
  protected $_ua = '';

  public function __construct($ua)
  {
    $this->_ua = (string) $ua;
  }

  public function __toString()
  {
    return $this->_ua;
  }

  public function isBot()
  {
    return $this->isMatch(sprintf('#(%s)#i', implode('|', static::BOTS)));
  }

  public function isMatch($inc, $exc = null, &$matches = [])
  {
    if (preg_match($inc, (string) $this, $matches))
    {
      if (empty($exc) || !preg_match($exc, (string) $this))
      {
        return true;
      }
    }

    return false;
  }

  /**
   * @param string        $inc        Regex Pattern to match this browser, including delimiters and options
   * @param string|null   $exc        Optional Regex Pattern of exclusions, including delimiters and options
   * @param \Closure|null $normalizer Normalizer for regex matches.  Must return an array.
   *
   * @return array|bool
   */
  public function getMatches($inc, $exc = null, $normalizer = null)
  {
    $matches = [];
    if ($this->isMatch($inc, $exc, $matches))
    {
      if ($normalizer instanceof \Closure)
      {
        return $normalizer($matches);
      }
      else
      {
        return $matches;
      }
    }

    return false;
  }
}
