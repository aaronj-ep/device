<?php

namespace DevCoding\Helper\Resolver;

use DevCoding\CodeObject\Resolver\ProjectResolver;
use DevCoding\Helper\Dependency\BrowserResolverAwareInterface;
use DevCoding\Helper\Dependency\BrowserResolverTrait;
use DevCoding\Parser\CanIUse\Agent;
use DevCoding\Parser\CanIUse\AgentFactory;
use DevCoding\Parser\CanIUse\Feature;
use DevCoding\Parser\CanIUse\FeatureFactory;

class FeatureResolver implements BrowserResolverAwareInterface
{
  use BrowserResolverTrait;

  /** @var Agent */
  protected $_Agent;
  /** @var FeatureFactory */
  protected $_FeatureFactory;
  /** @var Feature[] */
  protected $_Features = [];

  public function resolve($key_or_keys)
  {
    $arr = is_array($key_or_keys) ? $key_or_keys : [$key_or_keys];
    foreach ($arr as $key)
    {
      if ($val = $this->get($key))
      {
        return $val;
      }
    }

    return null;
  }

  public function get($key)
  {
    if ($Feature = $this->getFeature($key))
    {
      if ($Agent = $this->getAgent())
      {
        return $Feature->isSupported($Agent);
      }
    }

    return null;
  }

  /**
   * @return Agent
   */
  public function getAgent()
  {
    if (empty($this->_Agent))
    {
      if ($browser = $this->getBrowserObject())
      {
        $this->_Agent = (new AgentFactory())->build($browser);
      }
    }

    return $this->_Agent;
  }

  /**
   * @param string $key
   *
   * @return Feature|null
   */
  public function getFeature($key)
  {
    if (!isset($this->_Features[$key]))
    {
      $name = str_replace('CF-', '', $key);

      try
      {
        $this->_Features[$key] = $this->getFeatureFactory()->build($name);
      }
      catch (\Exception $e)
      {
        $this->_Features[$key] = false;
      }
    }

    return $this->_Features[$key];
  }

  /**
   * @return FeatureFactory
   */
  protected function getFeatureFactory()
  {
    if (!isset($this->_FeatureFactory))
    {
      $path = sprintf('%s/vendor/fyrd/caniuse/features-json', (new ProjectResolver())->getDir());
      $this->_FeatureFactory = new FeatureFactory($path);
    }

    return $this->_FeatureFactory;
  }
}