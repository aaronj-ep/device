<?php

namespace DevCoding\Hints;

use DevCoding\CodeObject\Resolver\ClassResolver;
use DevCoding\Hints\Feature\CommonFeature;
use DevCoding\Hints\Feature\CssGrid;
use DevCoding\Hints\Feature\Flexbox;
use DevCoding\Hints\Feature\ArrayIncludes;
use DevCoding\Hints\Feature\LoadingLazyAttr;
use DevCoding\Hints\Feature\Promises;
use DevCoding\Hints\Feature\Srcset;
use DevCoding\Hints\Feature\RareFeature;
use DevCoding\Hints\Feature as Feature;

class FeatureHints extends HintsAbstract
{
  // Feature Hints
  const CSS_FLEX          = Flexbox::KEY;
  const CSS_GRID          = CssGrid::KEY;
  const JS_ARRAY_INCLUDES = ArrayIncludes::KEY;
  const JS_PROMISE        = Promises::KEY;
  const HTML_LOADING      = LoadingLazyAttr::KEY;
  const HTML_SRCSET       = Srcset::KEY;

  public function __construct()
  {
    if (!$this->isWarmed())
    {
      static::warm();
    }
  }

  public function isSupported($key)
  {
    return $this->header($key);
  }

  /**
   * @return bool
   */
  public function isWarmed(): bool
  {
    return static::getenv('CF_WARMED') ?? false;
  }

  public function warm()
  {
    /** @var string[] $requested */
    $requested = static::getenv('FEATURE_HINTS');
    $needed    = [];

    foreach ($requested as $request)
    {
      if (!$this->getHeaderBag()->has($request))
      {
        if ($feature = $this->getConfiguredFeature($request))
        {
          $needed[] = $feature;
        }
        elseif ($feature = $this->resolveFeature($request))
        {
          $needed[] = $feature;
        }
      }
    }

    $this->getHeaderBag()->populate($needed);

    $this->setWarmed();
  }

  protected function setWarmed($state = true)
  {
    putenv(sprintf('FH_WARMED=%s', $state ? 1 : 0));

    return $this;
  }

  protected function getFeatures()
  {

  }

  /**
   * @param string $key
   *
   * @return mixed|null
   */
  protected function getConfiguredFeature($key)
  {
    $classes = new ClassResolver([Flex::class, Loading::class, Promise::class]);
    foreach ($classes as $class)
    {
      if (defined($class::KEY) && $class::KEY === $key)
      {
        return $this->configure(new $class());
      }
    }

    return null;
  }

  /**
   * @param $key
   *
   * @return CommonFeature|RareFeature
   */
  protected function resolveFeature($key)
  {
    if ($feature = $this->_FeatureResolver->getFeature($key))
    {
      if ($feature->getUsage() >= 85)
      {
        return $this->configure(new CommonFeature($key));
      }
      else
      {
        return $this->configure(new RareFeature($key));
      }
    }

    return null;
  }

  protected function getFeatureResolver()
  {
    return $this->_FeatureResolver;
  }

  protected function getBrowserResolver()
  {
    return $this->_BrowserResolver;
  }
}
