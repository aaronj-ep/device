<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Helper\Resolver\ConfigBag;

trait ConfigBagTrait
{
  /** @var ConfigBag */
  protected $_ConfigBag;

  public function getConfigBag(): ConfigBag
  {
    return $this->_ConfigBag;
  }

  public function setConfigBag(ConfigBag $ConfigBag): ConfigBagAwareInterface
  {
    $this->_ConfigBag = $ConfigBag;

    return $this;
  }
}