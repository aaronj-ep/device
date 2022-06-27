<?php

namespace DevCoding\Helper\Dependency;

trait DependencyTrait
{
  protected function configure($object)
  {
    $getMessage = function($method) use ($object)
    {
      return sprintf(
          '%s must have a "%s" method to configure %s',
          get_class($this),
          $method,
          get_class($object)
      );
    };

    if ($object instanceof ClientHintsAwareInterface)
    {
      if (!method_exists($this, 'getClientHints'))
      {
        throw new \Exception($getMessage('getClientHints'));
      }

      $object->setClientHints($this->getClientHints());
    }

    if ($object instanceof FeatureHintsAwareInterface)
    {
      if (!method_exists($this, 'getFeatureHints'))
      {
        throw new \Exception($getMessage('getFeatureHints'));
      }

      $object->setFeatureHints($this->getFeatureHints());
    }

    if ($object instanceof HeaderBagAwareInterface)
    {
      if (!method_exists($this, 'getHeaderBag'))
      {
        throw new \Exception($getMessage('getHeaderBag'));
      }

      $object->setHeaderBag($this->getHeaderBag());
    }

    if ($object instanceof CookieBagAwareInterface)
    {
      if (!method_exists($this, 'getCookieBag'))
      {
        throw new \Exception($getMessage('getCookieBag'));
      }

      $object->setCookieBag($this->getCookieBag());
    }

    if ($object instanceof PlatformResolverAwareInterface)
    {
      if (!method_exists($this, 'getPlatformResolver'))
      {
        throw new \Exception($getMessage('getPlatformResolver'));
      }

      $object->setPlatformResolver($this->getPlatformResolver());
    }

    if ($object instanceof BrowserResolverAwareInterface)
    {
      if (!method_exists($this, 'getBrowserResolver'))
      {
        throw new \Exception($getMessage('getBrowserResolver'));
      }

      $object->setBrowserResolver($this->getBrowserResolver());
    }

    if ($object instanceof FeatureResolverAwareInterface)
    {
      if (!method_exists($this, 'getFeatureResolver'))
      {
        throw new \Exception($getMessage('getFeatureResolver'));
      }

      $object->setFeatureResolver($this->getFeatureResolver());
    }

    return $object;
  }
}