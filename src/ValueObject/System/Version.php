<?php

namespace DevCoding\ValueObject\System;

/**
 * Class Version.
 */
class Version
{
  const MAJOR = 'major';
  const MINOR = 'minor';
  const PATCH = 'patch';

  /** @var array */
  protected $_v = [self::MAJOR => null, self::MINOR => null, self::PATCH => null];

  // region //////////////////////////////////////////////// Instantiation Methods

  public function __construct($version)
  {
    $this->initialize($version);
  }

  public static function fromArray(array $arr)
  {
    return new Version($arr);
  }

  public static function fromString(string $str)
  {
    return new Version($str);
  }

  // endregion ///////////////////////////////////////////// End Instantiation Methods

  // region //////////////////////////////////////////////// Public Getters

  public function __toString()
  {
    return trim(implode('.', $this->toArray(false)), '.\s');
  }

  public function getMajor()
  {
    return $this->_v[self::MAJOR] ?? null;
  }

  public function getMinor()
  {
    return $this->_v[self::MINOR] ?? null;
  }

  public function getPatch()
  {
    return $this->_v[self::PATCH] ?? null;
  }

  public function toArray($withKeys = true)
  {
    return ($withKeys) ? $this->_v : [$this->getMajor(), $this->getMinor(), $this->getPatch()];
  }

  public function toString()
  {
    return (string) $this;
  }

  // endregion ///////////////////////////////////////////// End Public Getters

  /**
   * @param array $version
   *
   * @return $this
   */
  protected function initialize($version)
  {
    $this->_v = $this->normalize($version);

    return $this;
  }

  /**
   * Parses a semantic version number into an array.  If it is already an array, the array is parsed to normalize.
   *
   * @param array|string $v the version number as a string or an array
   *
   * @return array The version number as [major => ##, minor => ##, patch => ##]
   */
  protected function normalize($v)
  {
    if (!is_array($v))
    {
      try
      {
        $arr = explode('.', (string) $v);
      }
      catch (\Exception $e)
      {
        $msg = sprintf('%s values must be scalar or an array, not "%s"', __CLASS__, gettype($v));

        throw new \Exception($msg, null, $e);
      }
    }
    else
    {
      $arr = $v;
      if (isset($v[self::MAJOR], $v[self::MINOR], $v[self::PATCH]))
      {
        $arr = [
            $v[self::MAJOR] ?? 1,
            $v[self::MINOR] ?? null,
            $v[self::PATCH] ?? null,
        ];
      }
    }

    $major = array_shift($arr);
    $minor = !empty($arr) ? array_shift($arr) : 0;
    $patch = !empty($arr) ? implode('.', $arr) : 0;

    return [self::MAJOR => $major, self::MINOR => $minor, self::PATCH => $patch];
  }

  // endregion ///////////////////////////////////////////// End Public Getters
}
