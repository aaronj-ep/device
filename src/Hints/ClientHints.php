<?php

namespace DevCoding\Hints;

use DevCoding\Client\Object\Version\ClientVersion;
use DevCoding\Helper\Dependency\DependencyTrait;
use DevCoding\Helper\Dependency\PlatformResolverAwareInterface;
use DevCoding\Helper\Resolver\BrowserResolver;
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
  use DependencyTrait;
  use PlatformResolverTrait;
  use UserAgentTrait;

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

  public function __construct()
  {
    if (!$this->isWarmed())
    {
      static::warm();
    }
  }

  /**
   * @param $key
   *
   * @return float|int|string|bool
   */
  public function get($key)
  {
    return $this->header($key);
  }

  public function warm()
  {
    /** @var string[] $requested */
    $requested   = static::getenv('CLIENT_HINTS');
    $HeaderBag   = new HeaderBag();
    $Factory     = new HintFactory($HeaderBag);
    $needed      = $Factory->getConfiguredHints($requested);

    $static = array();
    $vary = array();
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
    return $this->getPlatformObject();
  }

  /**
   * @return ClientVersion
   */
  public function getPlatformVersion()
  {
    return $this->getPlatformObject()->getVersion();
  }

  /**
   * @return PointerObject
   */
  public function getPointer()
  {
    if ($pointer = $this->header(self::POINTER))
    {
      // Touch and Metro Here Please
      return new PointerObject($pointer);
    }
    else
    {
      return (new Hint\Pointer())->setHeaderBag($this->getHeaderBag())->getObject();
    }
  }

  public function getRemoteAddress()
  {
    return $this->header(['REMOTE_ADDR', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'X-REAL-IP']);
  }

  /**
   * @return UserAgentString
   */
  public function getUserAgent()
  {
    if ($ua = $this->header('User-Agent'))
    {
      return new UserAgentString($ua);
    }
    else
    {
      return (new Hint\LegacyUserAgent())->setHeaderBag($this->getHeaderBag())->getObject();
    }
  }

  public function isHinted()
  {

  }

  protected function setWarmed($state = true)
  {
    putenv(sprintf('CH_WARMED=%s', $state ? 1 : 0));

    return $this;
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
    return $this->_PlatformResolver;
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
