<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\HeaderBagHint;

class RemoteAddress extends HeaderBagHint
{
  public function get()
  {
    $this->header(['REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'X-REAL-IP']);
  }

  public function getDefault()
  {
    return '127.0.0.1';
  }

  public function isNative()
  {
    return true;
  }

  public function isStatic()
  {
    return true;
  }

  public function isVendor()
  {
    return false;
  }

  public function isDraft()
  {
    return false;
  }
}
