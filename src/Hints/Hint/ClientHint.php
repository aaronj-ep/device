<?php

namespace DevCoding\Hints\Hint;

use DevCoding\Hints\Base\ArgumentAwareInterface;
use DevCoding\Hints\Base\BrowserHintInterface;
use DevCoding\Hints\Base\BrowserHintTrait;
use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\CookieHintInterface;
use DevCoding\Hints\Base\CookieHintTrait;

class ClientHint extends Hint implements CookieHintInterface, BrowserHintInterface, ArgumentAwareInterface
{
  use CookieHintTrait;
  use BrowserHintTrait;
}
