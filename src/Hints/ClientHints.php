<?php

namespace DevCoding\Hints;

use DevCoding\Client\Object\Version\ClientVersion;
use DevCoding\Helper\Dependency\DependencyTrait;
use DevCoding\Helper\Dependency\PlatformResolverAwareInterface;
use DevCoding\Helper\Dependency\ServiceBag;
use DevCoding\Helper\Resolver\BrowserResolver;
use DevCoding\Helper\Resolver\ConfigBag;
use DevCoding\Helper\Resolver\CookieBag;
use DevCoding\Helper\Resolver\PlatformResolver;
use DevCoding\Hints\Hint as Hint;
use DevCoding\Helper\Dependency\PlatformResolverTrait;
use DevCoding\Hints\Base\UserAgentTrait;
use DevCoding\Hints\Factory\HintFactory;
use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Client\Object\Headers\UserAgentString;
use DevCoding\Client\Object\Platform\PlatformImmutable as PlatformObject;
use DevCoding\Client\Object\Hardware\Pointer as PointerObject;

class ClientHints extends HintsAbstract implements PlatformResolverAwareInterface
{
  use PlatformResolverTrait;
  use UserAgentTrait;

  /** @var Hint[] */
  protected $hints;
  /** @var $ServiceBag */
  protected $container;

  const DPR            = Hint\DPR::KEY;
  const ECT            = Hint\ECT::KEY;
  const WIDTH          = Hint\Width::KEY;
  const PLATFORM       = Hint\Platform::KEY;
  const SAVE_DATA      = Hint\SaveData::KEY;
  const UA             = Hint\UserAgent::KEY;
  const VIEWPORT_WIDTH = Hint\ViewportWidth::KEY;
  const REMOTE_ADDR    = Hint\RemoteAddr::KEY;

  // Draft Hints
  const ARCH                 = Hint\Arch::KEY;
  const BITNESS              = Hint\Bitness::KEY;
  const COLOR_SCHEME         = Hint\ColorScheme::KEY;
  const CONTRAST             = Hint\Contrast::KEY;
  const DEVICE_MEMORY        = Hint\DeviceMemory::KEY;
  const MODEL                = Hint\Model::KEY;
  const REDUCED_DATA         = Hint\ReducedData::KEY;
  const REDUCED_MOTION       = Hint\ReducedMotion::KEY;
  const REDUCED_TRANSPARENCY = Hint\ReducedTransparency::KEY;

  // Additional Hints
  const HEIGHT          = Hint\Height::KEY;
  const POINTER         = Hint\Pointer::KEY;
  const VIEWPORT_HEIGHT = Hint\ViewportHeight::KEY;

  /**
   * @param ConfigBag       $ConfigBag
   * @param HeaderBag|null  $HeaderBag
   * @param CookieBag|null  $CookieBag
   */
  public function __construct(ConfigBag $ConfigBag, $HeaderBag = null, $CookieBag = null)
  {
    $this->container = new ServiceBag(array_filter(func_get_args()));

    foreach($this->config()->get('hints') as $config)
    {
      $class = $config['class'] ?? ClientHint::class;

      if (!is_a($class, Hint::class, true))
      {
        throw new \InvalidArgumentException(sprintf('Class "%s" does not extend "%s".', $class, Hint::class));
      }

      // Instantiate the hint
      $hint = is_a($class, ArgumentAwareInterface::class, true) ? new $class($config) : new $class();
      // Add it to the hints array
      $this->hints[$hint->config()->header] = $hint;
    }
  }

  /**
   * @param $key
   *
   * @return float|int|string|bool
   */
  public function get($key)
  {
    return $this->container->get(HeaderBag::class)->resolve($key);
  }

  public function warm()
  {
    /** @var string[] $requested */
    $env       = static::getenv('CLIENT_HINTS') ?? '';
    $requested = explode(", ", $env);
    $HeaderBag = $this->container->get(HeaderBag::class);
    $Factory   = new HintFactory($HeaderBag);
    $needed    = $Factory->getConfiguredHints([]);

    $static = [];
    $vary   = [];
    foreach ($needed as $key => $class)
    {
      $Hint = $Factory->get($class);

      if ($Hint->isStatic())
      {
        $static[$key] = $Hint->get();
      }
      else
      {
        $vary[$key] = $Hint->get();
      }
    }

    $HeaderBag->populate($static);
    $HeaderBag->populate($vary);

    $this->setWarmed();
  }

  /**
   * @return PlatformObject;
   */
  public function getPlatform()
  {
    return $this->getPlatformResolver()->getObject();
  }

  /**
   * @return ClientVersion
   */
  public function getPlatformVersion()
  {
    return $this->getPlatformResolver()->getObject()->getVersion();
  }

  /**
   * @return PointerObject
   */
  public function getPointer()
  {
    if ($pointer = $this->container->get(HeaderBag::class)->resolve(self::POINTER))
    {
      // Touch and Metro Here Please
      return new PointerObject($pointer);
    }
    else
    {
      return (new Hint\Pointer())->setHeaderBag($this->container->get(HeaderBag::class))->getObject();
    }
  }

  public function getRemoteAddress()
  {
    return $this->container->get(HeaderBag::class)->resolve(['REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'X-REAL-IP']);
  }

  /**
   * @return UserAgentString
   */
  public function getUserAgent()
  {
    if ($ua = $this->container->get(HeaderBag::class)->resolve('User-Agent'))
    {
      return new UserAgentString($ua);
    }
    else
    {
      return (new Hint\LegacyUserAgent())->setHeaderBag($this->container->get(HeaderBag::class))->getObject();
    }
  }

  public function isHinted()
  {
    $env       = static::getenv('CLIENT_HINTS') ?? '';
    $requested = explode(", ", $env);

    foreach ($requested as $request)
    {
      if (!$this->container->get(HeaderBag::class)->get($request))
      {
        return false;
      }
    }

    return true;
  }

  protected function setWarmed($state = true)
  {
    putenv(sprintf('CH_WARMED=%s', $state ? 1 : 0));

    return $this;
  }

  /**
   * @return ConfigBag
   */
  protected function config(): ConfigBag
  {
    return $this->container->get(ConfigBag::class);
  }

  /**
   * @return bool
   */
  protected function isWarmed(): bool
  {
    return static::getenv('CH_WARMED') ?? false;
  }

  protected function getPlatformResolver()
  {
    return $this->container->assert(PlatformResolver::class)->get(PlatformResolver::class);
  }

  protected function getBrowserResolver()
  {
    return $this->container->assert(BrowserResolver::class)->get(BrowserResolver::class);
  }
}
