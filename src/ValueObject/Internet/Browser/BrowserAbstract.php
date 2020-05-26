<?php

namespace DevCoding\ValueObject\Internet\Browser;

use DevCoding\ValueObject\System\Version;

/**
 * Class BrowserAbstract.
 */

/**
 * Base class for Browser value objects to extend.
 *
 * Class BrowserAbstract
 *
 * @see     https://github.com/jonesiscoding/device
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 * @package DevCoding\ValueObject\Internet\Browser
 */
abstract class BrowserAbstract
{
  const KEY_NAME     = 'name';
  const KEY_VERSION  = 'version';
  const KEY_FEATURES = 'features';
  const KEY_MOBILE   = 'mobile';

  /** @var Version */
  protected $_version = null;
  /** @var bool */
  protected $_mobile = false;
  /** @var string */
  protected $_name = '';
  /** @var array */
  protected $_versions = [];

  /**
   * Must provide an array of feature => version containing the minimum versions for each feature. If these versions
   * differ for mobile & desktop, an array of [desktop => [feature => ver], mobile => [feature => ver]] should be used.
   *
   * May return an empty array if there are no preconfigured defaults, and all version information is being injected.
   *
   * @return array
   */
  abstract public function getFeatureDefaults();

  // region //////////////////////////////////////////////// Instantiation Methods

  /**
   * This is private and final to prevent overrides and direct instantiation.  Instantiation should be done from the
   * static "create" or "fromUserAgent" methods.
   *
   * @throws \Exception If the class does not implement a static function fromUserAgent(string $ua)
   */
  final protected function __construct()
  {
    try
    {
      $func = new \ReflectionMethod($this, 'fromUserAgent');
      if (!$func->isStatic())
      {
        throw new \ReflectionException(sprintf('The "fromUserAgent" function must be declared static in "%s"', static::class));
      }
    }
    catch (\ReflectionException $exception)
    {
      throw new \Exception(sprintf('Classes extending "%s" must delare a static function "fromUserAgent".', __CLASS__));
    }
  }

  /**
   * @param array $config
   *
   * @return static
   *
   * @throws \Exception
   */
  public static function create($config)
  {
    $Browser = new static();
    if (!defined("$Browser::NAME"))
    {
      if (empty($config[$Browser::KEY_NAME]))
      {
        throw new \Exception(sprintf('A "name" key is required when creating a "%s"', static::class));
      }
      else
      {
        $Browser->setName($config[$Browser::KEY_NAME]);
      }
    }

    if (isset($config[$Browser::KEY_VERSION]))
    {
      $Browser->setVersion($config[$Browser::KEY_VERSION]);
    }
    if (isset($config[$Browser::KEY_MOBILE]))
    {
      $Browser->setMobile($config[$Browser::KEY_MOBILE]);
    }
    if (isset($config[$Browser::KEY_FEATURES]))
    {
      $Browser->setVersion($config[$Browser::KEY_FEATURES]);
    }

    return $Browser;
  }

  // endregion ///////////////////////////////////////////// End Instantiation Methods

  // region //////////////////////////////////////////////// Getters

  public function __toString()
  {
    return trim(implode('/', [$this->getName(), $this->getVersion()]), './');
  }

  /**
   * Returns the common name of the browser this object represents.
   *
   * @return string
   */
  public function getName()
  {
    return $this->_name;
  }

  /**
   * @return Version
   */
  public function getVersion()
  {
    return $this->_version;
  }

  /**
   * @return bool
   */
  public function isMobile()
  {
    return $this->_mobile;
  }

  /**
   * Determines if the given feature is supported by this browser, based on the _features property.
   *
   * @param string $feature
   *
   * @return bool|null
   */
  public function isSupported($feature)
  {
    if ($ver = $this->getVersionForFeature($feature))
    {
      return $this->isVersionUp($ver[0], $ver[1]);
    }

    return null;
  }

  /**
   * Determines if this browser matches the given version or greater.
   *
   * @param int $cMajor
   * @param int $cMinor
   *
   * @return bool
   */
  public function isVersionUp($cMajor, $cMinor = null)
  {
    if (is_null($this->_version))
    {
      return false;
    }

    $bMajor = $this->getVersion()->getMajor();
    $bMinor = $this->getVersion()->getMinor();

    if ($bMajor > $cMajor)
    {
      return true;
    }
    elseif ($bMajor < $cMajor)
    {
      return false;
    }
    elseif ($bMajor == $cMajor && !is_null($cMinor))
    {
      if ($bMinor >= $cMinor)
      {
        return true;
      }
      else
      {
        return false;
      }
    }
    else
    {
      return true;
    }
  }

  // endregion ///////////////////////////////////////////// End Getters

  // region //////////////////////////////////////////////// Setters

  public function setFeatures($features)
  {
    $this->_versions = $features;
  }

  /**
   * Sets whether or not this browser object represents a mobile browser.
   *
   * @param bool $mobile
   *
   * @return BrowserAbstract
   */
  public function setMobile($mobile)
  {
    $this->_mobile = $mobile;

    return $this;
  }

  /**
   * Sets this browser's common name.
   *
   * @param string $name
   *
   * @return BrowserAbstract
   */
  public function setName($name)
  {
    $this->_name = $name;

    return $this;
  }

  /**
   * Sets the version number for this browser object.
   *
   * @param array|string|Version $version
   *
   * @return BrowserAbstract
   */
  public function setVersion($version)
  {
    $this->_version = ($version instanceof Version) ? $version : new Version($version);

    return $this;
  }

  // endregion ///////////////////////////////////////////// End Setters

  protected function getVersionForFeature($key)
  {
    $defaults = $this->getFeatureDefaults();
    if ($this->isMobile())
    {
      $versions = (isset($this->_versions['mobile'])) ? $this->_versions['mobile'] : $this->_versions;
      $defaults = (isset($defaults['mobile'])) ? $defaults['mobile'] : $defaults;
    }
    else
    {
      $versions = (isset($this->_versions['desktop'])) ? $this->_versions['desktop'] : $this->_versions;
      $defaults = (isset($defaults['desktop'])) ? $defaults['desktop'] : $defaults;
    }

    $combined = $versions + $defaults;

    if (isset($combined[$key]))
    {
      if (is_bool($combined[$key]))
      {
        // If TRUE, return lowest (should always match).  If FALSE, return highest (should never match)
        return ($combined[$key]) ? [0, 1, 0] : [PHP_INT_MAX, 0, 0];
      }
      else
      {
        return $this->parseVersion($combined[$key]);
      }
    }

    return null;
  }

  /**
   * Parses a semantic version number into an array.  If it is already an array, uses the first two elements as the
   * major and minor versions, and the remaining elements as the revision.
   *
   * @param array|string $v the version number as a string or an array
   *
   * @return array The version number as [major, minor, revision]
   */
  protected function parseVersion($v)
  {
    if (!is_array($v))
    {
      $v = explode('.', $v);
    }

    $major = array_shift($v);
    $minor = array_shift($v);
    $rev   = implode('.', $v);

    return [$major, $minor, $rev];
  }
}
