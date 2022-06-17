<?php

namespace DevCoding\Hints\Base;

abstract class AbstractHint
{
  /**
   * @return string
   */
  public static function key()
  {
    if (defined(static::KEY))
    {
      return static::KEY;
    }
    else
    {
      return explode('\\', static::class)[0];
    }
  }

  /**
   * @return string|int|float|bool|null
   */
  abstract public function get();

  /**
   * @return string|int|float|bool|null
   */
  abstract public function getDefault();

  /**
   * @return bool
   */
  abstract public function isNative();

  /**
   * @return bool
   */
  abstract public function isVendor();

  /**
   * @return bool
   */
  abstract public function isDraft();

  abstract public function isStatic();


}
