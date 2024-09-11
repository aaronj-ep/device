<?php

namespace DevCoding\Helper\Resolver;

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

  /**
   * Returns the array of bot regex patterns, for use with ClientHints
   *
   * @return array
   */
  public function getBots(): array
  {
    return $this->has('bots') ? $this->getArrayCopy()['bots'] : [];
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

  /**
   * Returns the configured array of features that can or are polyfilled in your application, identified by their
   * configured header.
   *
   * @return array
   */
  public function getPolyfill(): array
  {
    return $this->has('polyfill') ? $this->get('polyfill') : [];
  }

  /**
   * Returns the configured array of required features in your application, identified by their configured header.
   *
   * @return array
   */
  public function getRequire(): array
  {
    return $this->has('require') ? $this->get('require') : [];
  }
}
