<?php
/**
 * HintResolver.php.
 */

namespace DevCoding\Device;

/**
 * Resolves hints for child classes.
 *
 * Class HintResolver
 *
 * @see     https://github.com/jonesiscoding/device
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 */
abstract class HintResolver
{
  /** @var Hints|HintsWithFallback */
  protected $_hints = null;

  public function __construct($hints = null)
  {
    $this->setHints($hints);
  }

  // region //////////////////////////////////////////////// Feature Getter Methods

  /**
   * Retrieves the given header, if it is set.
   *
   * @param string $key
   *
   * @return int|string|null
   */
  protected function getHeader($key)
  {
    return $this->getHints()->getHeader($key);
  }

  /**
   * Retrieves the given hint by key, if it is set.
   *
   * @param $key
   *
   * @return array|bool|mixed|null
   */
  public function getHint($key)
  {
    return $this->getHints()->get($key);
  }

  // endregion ///////////////////////////////////////////// End Feature Getter Methods

  // region //////////////////////////////////////////////// Hint Helper Methods

  /**
   * @return Hints|HintsWithFallback
   */
  public function getHints()
  {
    if (!$this->_hints instanceof Hints)
    {
      $this->_hints = new HintsWithFallback();
    }

    return $this->_hints;
  }

  /**
   * @param Hints $deviceHints
   *
   * @return $this
   */
  public function setHints($deviceHints)
  {
    if ($deviceHints && $deviceHints instanceof Hints)
    {
      $this->_hints = $deviceHints;
    }
    elseif ($deviceHints)
    {
      throw new \Exception('Hints must be a subclass or instance of '.Hints::class);
    }

    return $this;
  }

  // endregion ///////////////////////////////////////////// End Hint Helper Methods
}
