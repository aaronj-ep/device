<?php

namespace DevCoding\Hints\Base;

use DevCoding\Helper\Resolver\CookieBag;

abstract class Hint implements HeaderHintInterface
{
  use HeaderHintTrait;

  /** @var HintConfig */
  public $config;

  final public function __construct()
  {
    if ($this instanceof ConstantAwareInterface)
    {
      $this->__constants();
    }
    elseif ($this instanceof ArgumentAwareInterface)
    {
      $this->__arguments(func_get_arg(0));
    }
    else
    {
      $this->config = new HintConfig();
    }

    if ($this instanceof CookieHintInterface && !isset($this->config->cookie))
    {
      // Get a copy of the current config
      $config = $this->config->getArrayCopy();
      // Set the cookie key, based on the header
      $config['cookie'] = CookieBag::key($this->config->header);
      // Set the config property again
      $this->config = new HintConfig($config);
    }
  }

  /**
   * Configures this object using specific class constants
   *
   * @return void
   */
  private function __constants()
  {
    $this->__arguments((new \ReflectionClass($this))->getConstants(), false);

    if (!isset($this->config->default))
    {
      throw new \LogicException(sprintf('%s::DEFAULT must be defined.', get_class($this)));
    }

    if (!isset($this->config->header))
    {
      throw new \LogicException(sprintf('%s::HEADER must be defined.', get_class($this)));
    }
  }

  /**
   * Normalizes and configures the object; used by the constructor.
   *
   * @param array $config   A configuration array with keys that match the properties in this class.
   * @param bool  $validate If the configuration should be validated.
   *
   * @return void
   */
  private function __arguments(array $config, $validate = true)
  {
    if ($this instanceof ListValueInterface)
    {
      $config['format'] = 'list';
    }
    elseif ($this instanceof BooleanValueInterface)
    {
      $config['format'] = 'bool';
    }

    foreach($config as $name => $value)
    {
      $config[strtolower($name)] = $value;
    }

    $this->config = new HintConfig($config);

    if ($validate)
    {
      if (!isset($this->config->default))
      {
        throw new \InvalidArgumentException(
          sprintf('The %s option is required in %s.', HintConfig::DEFAULT, get_class($this))
        );
      }

      if (!isset($this->config->header))
      {
        throw new \InvalidArgumentException(
          sprintf('The %s option is required in %s.', HintConfig::HEADER, get_class($this))
        );
      }
    }
  }

  public function config(): HintConfig
  {
    return $this->config;
  }

  public function default()
  {
    return $this->config->default;
  }
}
