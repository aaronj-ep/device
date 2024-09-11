<?php

namespace DevCoding\Device;

use DevCoding\Helper\Dependency\ServiceBag;
use DevCoding\Helper\Resolver\ConfigBag;
use DevCoding\Helper\Resolver\CookieBag;
use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Hints\ClientHints;
use DevCoding\Hints\Hint\DeviceMemory;
use DevCoding\Hints\Hint\DPR;
use DevCoding\Hints\Hint\ECT;
use DevCoding\Hints\Hint\Height;
use DevCoding\Hints\Hint\Model;
use DevCoding\Hints\Hint\Width;

/**
 * Device.
 *
 * Class Device
 *
 * @package DevCoding\Device
 */
class Device
{
  /** @var ServiceBag */
  protected $container;

  /**
   * @param ConfigBag $ConfigBag
   */
  public function __construct(ConfigBag $ConfigBag)
  {
    $this->container = new ServiceBag([ConfigBag::class => $ConfigBag]);

    if ($this->container->get(ConfigBag::class)->get('warm') && 'cli' !== php_sapi_name())
    {
      $this->getClientHints()->warm();
    }
  }

  /**
   * @param array $config
   *
   * @return static
   */
  public static function create($config = [])
  {
    return new static(new ConfigBag($config));
  }

  // region //////////////////////////////////////////////// Hardware Getters

  /**
   * @return string|null
   */
  public function getModel()
  {
    return $this->getClientHints()->get(Model::HEADER);
  }

  /**
   * @return float|int
   */
  public function getDeviceMemory()
  {
    return $this->getClientHints()->get(DeviceMemory::HEADER);
  }

  /**
   * @return float|int
   */
  public function getDevicePixelRatio()
  {
    return $this->getClientHints()->get(DPR::HEADER);
  }

  /**
   * @return string
   */
  public function getEffectiveConnectionType()
  {
    return $this->getClientHints()->get(ECT::HEADER);
  }

  /**
   * @return float|int
   */
  public function getHeight()
  {
    return $this->getClientHints()->get(Height::HEADER);
  }

  /**
   * @return float|int
   */
  public function getWidth()
  {
    return $this->getClientHints()->get(Width::HEADER);
  }

  // endregion ///////////////////////////////////////////// End Hardware Getters

  // region //////////////////////////////////////////////// Subset Getters

  /**
   * @return Client
   */
  public function Client()
  {
    return $this->get(Client::class);
  }

  /**
   * @return Screen
   */
  public function Screen()
  {
    return $this->get(Screen::class);
  }

  /**
   * @return Platform
   */
  public function Platform()
  {
    return $this->get(Platform::class);
  }

  /**
   * @return Preferences
   */
  public function Preferences()
  {
    return $this->get(Preferences::class);
  }

  // endregion ///////////////////////////////////////////// End Subset Getters

  // region //////////////////////////////////////////////// Requirement Methods

  /**
   * Evaluates if the current client lacks one or more of the features indiciated as required by the configuration.
   * If polyfills are specified in the configuration, they are taken into account when evaluating the client.
   *
   * @return bool
   */
  public function isSunset(): bool
  {
    /** @var ConfigBag $ConfigBag */
    $ConfigBag = $this->container->get(ConfigBag::class);
    $required  = $ConfigBag->getRequire();
    if (!empty($required))
    {
      $polyfills = $ConfigBag->getPolyfill();
      foreach($ConfigBag->get('require') as $key => $value)
      {
        if (!$this->isValid($key, $value))
        {
          if (!in_array($key, $polyfills))
          {
            return true;
          }
        }
      }
    }

    return false;
  }

  /**
   * Evaluates if the configured polyfills are needed, based on the 'require' and 'polyfill' configurations.
   *
   * @return bool
   */
  public function isPolyfill()
  {
    $ConfigBag = $this->container->get(ConfigBag::class);
    $required  = $ConfigBag->getRequire();
    if (!empty($required))
    {
      $polyfills = $ConfigBag->getPolyfill();
      foreach($required as $key => $value)
      {
        if (!$this->isValid($key, $value))
        {
          if (in_array($key, $polyfills))
          {
            return true;
          }
        }
      }
    }

    return false;
  }

  // endregion ///////////////////////////////////////////// End Requisite Methods

  // region //////////////////////////////////////////////// Helper Methods

  /**
   * @param $id
   *
   * @return mixed|object
   */
  protected function get($id)
  {
    return $this->container->assert($id)->get($id);
  }

  /**
   * @return ClientHints
   */
  public function getClientHints()
  {
    if (!$this->container->has(ClientHints::class))
    {
      $config = $this->container->get(ConfigBag::class);
      $header = $this->container->assert(HeaderBag::class)->get(HeaderBag::class);

      if ($config->has('cookie'))
      {
        $cookie = $this->container->assert(new CookieBag($config->get('cookie')))->get(CookieBag::class);
      }
      else
      {
        $cookie = $this->container->assert(CookieBag::class)->get(CookieBag::class);
      }

      $this->container->assert(new ClientHints($config, $header, $cookie));
    }

    return $this->get(ClientHints::class);
  }

  /**
   * @param string $key
   * @param mixed $expected
   *
   * @return bool
   */
  protected function isValid($key, $expected)
  {
    $ClientHints = $this->getClientHints();
    if ($ClientHints->has($key))
    {
      $hint = is_bool($expected) ? $ClientHints->bool($key) : $ClientHints->get($key);
      if (is_bool($expected))
      {
        return ($hint === $expected);
      }
      elseif (is_numeric($expected))
      {
        return $hint >= $expected;
      }
      elseif (is_string($expected))
      {
        return $hint == $expected;
      }
      else
      {
        return !empty($value);
      }
    }

    return false;
  }

  // endregion ///////////////////////////////////////////// End Helper Methods
}
