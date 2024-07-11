<?php

namespace DevCoding\Device;

use DevCoding\Helper\Dependency\ServiceBag;
use DevCoding\Helper\Resolver\ConfigBag;
use DevCoding\Helper\Resolver\CookieBag;
use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Hints\ClientHints;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
   * @param array $config
   */
  public function __construct($config = array())
  {
    $this->container = new ServiceBag([new ConfigBag($config)]);
  }

  /**
   * @param array $config
   *
   * @return static
   */
  public static function create($config = array())
  {
    return new static($config);
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
  protected function getClientHints()
  {
    return $this->get(ClientHints::class);
  }

  // endregion ///////////////////////////////////////////// End Helper Methods
}
