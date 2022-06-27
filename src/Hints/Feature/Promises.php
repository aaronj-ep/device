<?php

namespace DevCoding\Hints\Feature;

use DevCoding\Hints\Base\AbstractFeature;
use DevCoding\Helper\Dependency\BrowserResolverTrait;
use DevCoding\Helper\Dependency\FeatureResolverAwareInterface;
use DevCoding\Helper\Dependency\FeatureBagTrait;

class Promises extends AbstractFeature implements FeatureResolverAwareInterface
{
  use FeatureBagTrait;
  use BrowserResolverTrait;

  const KEY = 'CF-Promises';

  public function is()
  {
    return $this->cookie('f.jp') ?? $this->feature(self::KEY) ?? $this->getDefault();
  }

  public function getDefault()
  {
    return false;
  }
}