<?php

namespace DevCoding\Helper\Resolver;

use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * Class ConfigBag
 *
 * @author  AMJones <am@jonesiscoding.com>
 * @package DevCoding\Device
 */
class ConfigBag extends \ArrayObject
{
  public function get(string $id)
  {
    return parent::offsetGet($id);
  }

  public function has(string $id): bool
  {
    return parent::offsetExists($id);
  }

  public function getHints(): array
  {
    return $this->has('hints') ? $this->getArrayCopy()['hints'] : [];
  }

  /**
   * Returns the array of browser configurations, for use with DevCoding\Client\Factory\BrowserFactory
   *
   * @return array
   */
  public function getBrowsers(): array
  {
    return $this->has('browsers') ? $this->getArrayCopy()['browsers'] : [];
  }
}
