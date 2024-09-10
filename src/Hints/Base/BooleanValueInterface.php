<?php

namespace DevCoding\Hints\Base;

interface BooleanValueInterface
{
  const TRUE  = '?1';
  const FALSE = '?0';

  public function bool($value);
}
