<?php

namespace DevCoding\Device;

use DevCoding\Helper\Dependency\ServiceBag;
use DevCoding\Helper\Resolver\ConfigBag;
use DevCoding\Helper\Resolver\CookieBag;
use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Hints\ClientHints;

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
    return $this->getClientHints()->get(ClientHints::MODEL);
  }

  /**
   * @return float|int
   */
  public function getDeviceMemory()
  {
    return $this->getClientHints()->get(ClientHints::DEVICE_MEMORY);
  }

  /**
   * @return float|int
   */
  public function getDevicePixelRatio()
  {
    return $this->getClientHints()->get(ClientHints::DPR);
  }

  /**
   * @return string
   */
  public function getEffectiveConnectionType()
  {
    return $this->getClientHints()->get(ClientHints::ECT);
  }

  /**
   * @return float|int
   */
  public function getHeight()
  {
    return $this->getClientHints()->get(ClientHints::HEIGHT);
  }

  /**
   * @return float|int
   */
  public function getWidth()
  {
    return $this->getClientHints()->get(ClientHints::WIDTH);
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
   * @return Hardware
   */
  public function Hardware()
  {
    return $this->get(Hardware::class);
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

  /**
   * @return bool
   */
  public function isHinted()
  {
    return $this->getHeaderBag()->isHinted() || $this->getCookieBag()->isHinted();
  }

  // endregion ///////////////////////////////////////////// End Subset Getters

  // region //////////////////////////////////////////////// Helper Methods

  /**
   * @param $id
   *
   * @return mixed|object
   * @throws ContainerExceptionInterface
   * @throws NotFoundExceptionInterface
   */
  protected function get($id)
  {
    return $this->container->assert($id)->get($id);
  }

  /**
   * @return HeaderBag
   */
  protected function getHeaderBag()
  {
    return $this->get(HeaderBag::class);
  }

  /**
   * @return CookieBag
   */
  protected function getCookieBag()
  {
    return $this->get(CookieBag::class);
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

  // endregion ///////////////////////////////////////////// End Helper Methods
}
