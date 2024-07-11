<?php

namespace DevCoding\Helper\Resolver;

use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * Class ConfigBag
 *
 * @author  AMJones <am@jonesiscoding.com>
 * @package DevCoding\Device
 */
class ConfigBag extends \ArrayObject implements PsrContainerInterface
{
  /**
   * {@inheritDoc}
   */
  public function get(string $id)
  {
    return parent::offsetGet($id);
  }

  /**
   * {@inheritDoc}
   */
  public function has(string $id): bool
  {
    return parent::offsetExists($id);
  }

  public function getFeatures(): array
  {
    return $this->has('features') ? $this->getArrayCopy()['features'] : array();
  }

  /**
   * Returns the array of browser configurations, for use with DevCoding\Client\Factory\BrowserFactory
   *
   * @return array
   */
  public function getBrowsers(): array
  {
    return $this->has('browsers') ? $this->getArrayCopy()['browsers'] : array();
  }
}