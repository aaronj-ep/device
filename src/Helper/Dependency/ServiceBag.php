<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\CodeObject\Object\ClassString;
use DevCoding\Helper\Resolver\BrowserResolver;
use DevCoding\Helper\Resolver\CookieBag;
use DevCoding\Helper\Dependency\DependencyBag;
use DevCoding\Helper\Resolver\FeatureResolver;
use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Helper\Resolver\PlatformResolver;
use DevCoding\Hints\ClientHints;
use DevCoding\Hints\FeatureHints;

class ServiceBag extends DependencyBag
{
  protected $map = [
      'BrowserResolver'  => BrowserResolver::class,
      'ClientHints'      => ClientHints::class,
      'CookieBag'        => CookieBag::class,
      'FeatureResolver'  => FeatureResolver::class,
      'FeatureHints'     => FeatureHints::class,
      'HeaderBag'        => HeaderBag::class,
      'PlatformResolver' => PlatformResolver::class
  ];

  /**
   * @param string $id
   *
   * @return $this
   * @throws \Exception
   */
  public function assert($id)
  {
    if (!$this->has($id))
    {
      $requires = $this->requires($id);

      if (!empty($requires))
      {
        foreach ($requires as $requirement)
        {
          $this->assert($requirement);
        }
      }

      $this->objects[$id] = $this->configure(new $id());
    }

    return $this;
  }

  public function configure($obj)
  {
    $requires = $this->requires(get_class($obj));
    foreach ($requires as $requirement)
    {
      $setter = $this->getSetter($requirement);
      $depend = $this->get($requirement);
      $obj->$setter($depend);
    }

    return $obj;
  }

  protected function requires($id)
  {
    $requires = [];
    $implements = $this->implements($id);

    foreach ($implements as $interface)
    {
      if (false !== strpos($interface, 'AwareInterface'))
      {
      $class = $this->getClass($interface);
      if ($class === $id)
      {
        throw new \Exception('Circular Reference!');
      }

      $requires[] = $class;
    }
    }

    return $requires;
  }

  protected function getSetter($class)
  {
    return 'set'.(new ClassString($class))->getName();
  }

  protected function getClass($interface)
  {
    $dep = str_replace('AwareInterface', '', (new ClassString($interface))->getName());
    if (isset($this->map[$dep]))
    {
      return $this->map[$dep];
    }

    $setter = 'set'.$dep;
    $reflect = new \ReflectionClass($interface);
    if ($reflect->hasMethod($setter))
    {
      $method     = $reflect->getMethod($setter);
      $parameters = $method->getParameters();
      foreach ($parameters as $parameter)
      {
        return (string) $parameter->getType();
      }
    }

    throw new \Exception(sprintf('%s must contain a method %s', $interface, $setter));
  }

  protected function implements($id)
  {
    $interfaces = class_implements($id);
    usort($interfaces, function($a, $b) {
      if($a == $b) {
        return 0;
      }

      $suba = class_implements($a);
      $subb = class_implements($b);

      if(empty($suba) && empty($subb))
      {
        return 0;
      }

      if(empty($suba) && !empty($subb)) {
        return -1;
      }

      if(empty($subb) && !empty($suba)) {
        return 1;
      }

      if(in_array($b, $suba) && !in_array($a, $subb))
      {
        return 1;
      }

      if(in_array($a, $subb) && !in_array($b, $suba)) {
        return -1;
      }

      return 0;
    });

    return $interfaces;
  }
}