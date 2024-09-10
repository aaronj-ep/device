<?php

namespace DevCoding\Hints\Base;

trait BooleanHintTrait
{
  public function bool($data)
  {
    if (is_int($data))
    {
      return (bool) $data;
    }

    if (is_scalar($data) || $this->isStringable($data))
    {
      $s = strtolower((string) $data);
      $t = ['true', '?1', 'reduce', 'yes', 'on', '1', 'y'];
      $f = ['false', '?0', 'no-preference', 'no', 'off', '0', 'n'];

      if (in_array($s, $t))
      {
        return true;
      }
      elseif (in_array($s, $f))
      {
        return false;
      }
    }

    return $data;
  }

  private function isStringable($data)
  {
    return is_object($data) && method_exists($data, '__toString');
  }
}
