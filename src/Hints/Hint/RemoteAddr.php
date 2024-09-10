<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\ConstantAwareInterface;

class RemoteAddr extends Hint implements ConstantAwareInterface
{
  const HEADER     = 'Remote-Addr';
  const ALTERNATES = ['CLIENT_IP', 'X_FORWARDED_FOR', 'X-REAL-IP'];
  const DEFAULT    = '127.0.0.1';
  const DRAFT      = false;
  const STATIC     = true;
  const VENDOR     = false;
}
