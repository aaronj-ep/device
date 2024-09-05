<?php

namespace DevCoding\Helper\ArrayObject;

/**
 * Trait to include in classes that extend \ArrayObject to effectively make them immutable.
 *
 * Trait ArrayObjectImmutableTrait
 *
 * @author  AMJones <am@jonesiscoding.com>
 * @package DevCoding\Helper
 */
trait ArrayObjectImmutableTrait
{
  public function append($value)
  {
    throw new LogicException(get_class($this).' objects are immutable, and cannot be appended.');
  }

  public function exchangeArray($array)
  {
    throw new LogicException(get_class($this).' objects are immutable, and cannot be exchanged.');
  }

  public function offsetSet($key, $value)
  {
    throw new LogicException(get_class($this).' objects are immutable, therefore the '.$key.' property is cannot be set.');
  }

  public function offsetUnset($key)
  {
    throw new LogicException(get_class($this).' objects are immutable, therefore the '.$key.' property is cannot be unset.');
  }
}
