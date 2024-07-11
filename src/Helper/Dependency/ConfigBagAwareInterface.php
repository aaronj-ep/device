<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Helper\Resolver\ConfigBag;

interface ConfigBagAwareInterface
{
  public function getConfigBag(): ConfigBag;

  public function setConfigBag(ConfigBag $config): ConfigBagAwareInterface;
}