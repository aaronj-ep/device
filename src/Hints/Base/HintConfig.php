<?php

namespace DevCoding\Hints\Base;

use DevCoding\Helper\ArrayObject\ArrayObjectImmutableTrait;

/**
 * @property string $header
 * @property string $cookie
 * @property string $key
 * @property bool   $draft
 * @property bool   $static
 * @property bool   $vendor
 * @property mixed  $default
 * @property array  $alternates
 */
class HintConfig extends \ArrayObject
{
  use ArrayObjectImmutableTrait;

  const HEADER     = 'header';
  const ALTERNATES = 'alternates';
  const COOKIE     = 'cookie';
  const KEY        = 'key';
  const DRAFT      = 'draft';
  const STATIC     = 'static';
  const VENDOR     = 'vendor';
  const DEFAULT    = 'default';

  public function __construct($array = [])
  {
    parent::__construct($array, \ArrayObject::ARRAY_AS_PROPS);
  }

  public function headers()
  {
    if ($this->offsetExists(self::ALTERNATES))
    {
      return array_merge([$this->header], $this->offsetGet(self::ALTERNATES));
    }

    return [$this->header];
  }
}
