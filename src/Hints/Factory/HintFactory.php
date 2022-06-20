<?php

namespace DevCoding\Hints\Factory;

use DevCoding\CodeObject\Resolver\ClassResolver;
use DevCoding\Helper\Dependency\BrowserResolverTrait;
use DevCoding\Helper\Dependency\CookieBagTrait;
use DevCoding\Helper\Dependency\DependencyTrait;
use DevCoding\Helper\Dependency\FeatureBagAwareInterface;
use DevCoding\Helper\Dependency\FeatureBagTrait;
use DevCoding\Helper\Dependency\HeaderBagTrait;
use DevCoding\Hints\Base\AbstractHint;
use DevCoding\Helper\Dependency\CookieBagAwareInterface;
use DevCoding\Helper\Dependency\HeaderBagAwareInterface;
use DevCoding\Helper\Resolver\BrowserResolver;
use DevCoding\Helper\Resolver\CookieBag;
use DevCoding\Client\Object\Headers\HeaderBag;

class HintFactory implements HeaderBagAwareInterface, CookieBagAwareInterface, FeatureBagAwareInterface
{
  use DependencyTrait;
  use HeaderBagTrait;
  use CookieBagTrait;
  use FeatureBagTrait;
  use BrowserResolverTrait;

  /** @var BrowserResolver */
  protected $_BrowserBag;
  /** @var string[] */
  protected $_HintClasses;
  /** @var object[] */
  protected $_Hints;

  /**
   * @param HeaderBag $HeaderBag
   */
  public function __construct($HeaderBag)
  {
    $this->_HeaderBag = $HeaderBag;
  }

  /**
   * @param $class
   *
   * @return AbstractHint
   */
  public function get($class)
  {
    if (!class_exists($class))
    {
      throw new \InvalidArgumentException(sprintf('The class "%s" was not found.', $class));
    }

    if (!isset($this->_Hints[$class]))
    {
      $this->_Hints[$class] = $this->configure(new $class());
    }

    return $this->_Hints[$class];
  }

  public function getConfiguredHints($filter = [])
  {
    $hints = [];
    foreach ($this->getHintClasses() as $class)
    {
      $key = $this->getHintClassKey($class);
      if (empty($filter) || in_array($key, $filter))
      {
        $hints[$key] = $class;
      }
    }

    return $hints;
  }

  protected function getHintClassKey($class)
  {
    if (defined($class::KEY))
    {
      return $class::KEY;
    }
    else
    {
      return explode('\\', $class)[0];
    }
  }

  /**
   * @return array
   */
  protected function getHintClasses()
  {
    if (!isset($this->_HintClasses))
    {
      $ClassResolver = new ClassResolver(['DevCoding\\Hints']);
      $this->_HintClasses = $ClassResolver->all();
    }

    return $this->_HintClasses;
  }

  protected function getAgentBag()
  {
    if (empty($this->_AgentBag))
    {
      $this->_AgentBag = new AgentBag();
    }

    return $this->_AgentBag;
  }

  /**
   * @return BrowserResolver
   */
  protected function getBrowserBag()
  {
    if (empty($this->_BrowserBag))
    {
      $this->_BrowserBag = new BrowserResolver();
    }

    return $this->_BrowserBag;
  }

  /**
   * @return HeaderBag
   */
  protected function getHeaderBag()
  {
    if (empty($this->_HeaderBag))
    {
      $this->_HeaderBag = new HeaderBag();
    }

    return $this->_HeaderBag;
  }

  protected function getCookieBag()
  {
    if (empty($this->_CookieBag))
    {
      $this->_CookieBag = new CookieBag();
    }

    return $this->_CookieBag;
  }
}
