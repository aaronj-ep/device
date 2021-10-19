<?php

namespace DevCoding\ValueObject\System;

use DevCoding\ValueObject\Internet\UserAgent;

class Platform
{
  const MACOS      = 'MacOS';
  const WIN_NT     = 'WinNT';
  const WIN_RT     = 'WinRT';
  const WIN_PHONE  = 'Windows Phone';
  const WIN_MOBILE = 'Windows Mobile';
  const CROS       = 'ChromeOS';
  const TVOS       = 'tvOS';
  const IOS        = 'iOS';
  const LINUX      = 'Linux';
  const ANDROID    = 'Android';

  protected $patterns = [
      self::CROS       => "#(?<name>CrOS).*Chrome\/(?<version>[0-9._]+)#",
      self::MACOS      => "#(?<name>Macintosh)[^\)0-9]*(?<version>[0-9_.]+)#",
      self::WIN_NT     => "#^(?!.*(6\.[23]; ARM|CE)).*(?<name>WinNT\s|Windows\sNT\s|Windows\s|Win\s?)(?<version>(2000|95|Vista|98|ME|XP|9x|[0-9]+\.[0-9_.]+)).*$#",
      self::WIN_RT     => "#(?<name>Windows\s*NT\s*)(?<version>6\.[23]);\s*ARM#",
      self::ANDROID    => "#^(?!.*(Windows Phone|IEMobile)).*(?<name>Android)[\s-\/]*(?<version>[0-9._]+).*$#i",
      self::WIN_PHONE  => "#(?<name>Windows Phone)\s*[OS|os]*\s*(?<version>[0-9_.]+)#",
      self::WIN_MOBILE => "#(?<name>Windows Mobile)\s*(?<version>[0-9_.]+)#",
      self::IOS        => "#(?<name>iPod touch|iPod|iPad|iPhone).+[OS|os][\s_](?<version>[0-9_.]+)#",
      self::LINUX      => '#^(?!.*(Win|Android|Darwin|T)).*(?<name>Linux).*$#i',
      self::TVOS       => "#(?<name>Apple\s?TV|tvOS)/?(?<version>[0-9_.]+)?#",
  ];

  /** @var string */
  protected $_name = '';
  /** @var Version */
  protected $_version;

  // region //////////////////////////////////////////////// Instantiation Methods

  /**
   * @param UserAgent|string $ua
   *
   * @return $this|null
   */
  public static function fromUserAgent($ua)
  {
    $UserAgent = ($ua instanceof UserAgent) ? $ua : new UserAgent($ua);
    $Platform  = new Platform();

    foreach ($Platform->getConfigured() as $key)
    {
      $pattern = $Platform->getPattern($key);
      if ($result = $UserAgent->getMatches($pattern))
      {
        $name = $result['name']    ?? 'Unknown';
        $verS = $result['version'] ?? null;

        switch ($key) {
          case self::IOS:
          case self::CROS:
          case self::TVOS:
            return $Platform->setName($key)->setVersion($verS);
          case self::MACOS:
            return $Platform->normalizeMacOs($result);
          case self::WIN_RT:
            return $Platform->setName($key)->setVersion(('6.2' == substr($verS, 0, 2)) ? '8' : '8.1');
          case self::WIN_NT:
            return $Platform->normalizeWindows($result);
          default:
            return $Platform->setName($name)->setVersion($verS);
        }
      }
    }

    return null;
  }

  // endregion ///////////////////////////////////////////// End Instantiation Methods

  // region //////////////////////////////////////////////// Public Getters / Setters

  public function __toString()
  {
    return trim(implode('/', [$this->getName(), $this->getVersion()]), './');
  }

  public function getMajor()
  {
    return $this->getVersion()->getMajor();
  }

  public function getMinor()
  {
    return $this->getVersion()->getMinor();
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->_name;
  }

  public function getPatch()
  {
    return $this->getVersion()->getPatch();
  }

  /**
   * @return Version
   */
  public function getVersion()
  {
    return $this->_version;
  }

  /**
   * @param string $key
   * @param string $pattern
   *
   * @return $this
   */
  public function setPattern($key, $pattern)
  {
    $this->patterns[$key] = $pattern;

    return $this;
  }

  // endregion ///////////////////////////////////////////// End Public Getters / Setters

  // region //////////////////////////////////////////////// Setters

  protected function setName(string $platform): Platform
  {
    $this->_name = $platform;

    return $this;
  }

  /**
   * @param array|string|Version $version
   *
   * @return $this
   */
  protected function setVersion($version): Platform
  {
    if (!$version instanceof Version)
    {
      if (is_array($version))
      {
        $version = Version::fromArray($version);
      }
      elseif (is_scalar($version) || is_object($version) && method_exists($version, '__toString'))
      {
        $version = Version::fromString(str_replace('_', '.', (string) $version));
      }
    }

    $this->_version = $version;

    return $this;
  }

  // endregion ///////////////////////////////////////////// End Public Setters

  // region //////////////////////////////////////////////// Helper Functions

  /**
   * @param $key
   *
   * @return string|null
   */
  protected function getPattern($key)
  {
    return $this->patterns[$key] ?? null;
  }

  /**
   * @return array
   */
  protected function getConfigured()
  {
    return array_keys($this->patterns);
  }

  /**
   * @param string $version
   *
   * @return $this
   */
  protected function normalizeDarwin($version)
  {
    $this->setName(self::MACOS);

    $DarwinVersion = Version::fromString($version);
    $patch         = implode('.', [$DarwinVersion->getMinor(), $DarwinVersion->getPatch()]);

    switch ((string) $DarwinVersion->getMajor()) {
      case '14': // Yosemite
        $this->setVersion([10, 10, $patch]);
        break;
      case '13': // Mavericks
        $this->setVersion([10, 9, $patch]);
        break;
      case '12': // Mountain Lion
        $this->setVersion([10, 8, $patch]);
        break;
      case '11': // Lion
        $this->setVersion([10, 7, $patch]);
        break;
      case '10': // Snow Leopard
        $this->setVersion([10, 6, $patch]);
        break;
      case '9': // Leopard
        $this->setVersion([10, 5, $patch]);
        break;
      default: // Build for Tiger & before don't seem to specify versions
        $this->setVersion([10, null, null]);
        break;
    }

    return $this;
  }

  /**
   * @param array $result
   *
   * @return $this
   */
  protected function normalizeMacOs($result)
  {
    $matched = $result[1] ?? '';
    $slug    = ($matched) ? str_replace([' ', '_', '-'], '', strtolower($matched)) : null;
    $verS    = isset($result[2]) ? (string) $result[2] : null;

    if ('darwin' === $slug || 'macowerpc' === $slug)
    {
      $this->normalizeDarwin($verS);
    }
    else
    {
      $this->setName(self::MACOS)->setVersion($verS);
    }

    return $this;
  }

  /**
   * @param array $result
   *
   * @return $this
   */
  protected function normalizeWindows($result)
  {
    $verS = isset($result['version']) ? (string) $result['version'] : null;
    $WinV = Version::fromString($verS);
    $norm = implode('.', [$WinV->getMajor(), $WinV->getMinor() ?: 0]);

    switch ($norm) {
          case '6.1':
            return $this->setName('Windows')->setVersion(7);
          case '6.2':
          case '8.0':
            return $this->setName('Windows')->setVersion(8);
          case '6.3':
          case '8.1':
            return $this->setName('Windows')->setVersion(8.1);
          case '6.4':
          case '10.0':
            return $this->setName('Windows')->setVersion(10);
          default:
            // Prior versions aren't worth tracking, they wouldn't have any features to worry about
            return $this->setName('Windows');
        }
  }

  // endregion ///////////////////////////////////////////// End Helper Functions
}
