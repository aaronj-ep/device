<?php

namespace DevCoding\Device;

class Hardware extends HintResolver
{
  /**
   * @return float|int
   */
  public function getDevicePixelRatio()
  {
    return $this->getHeader(Hints::HEADER_DPR) ?: $this->getHint(Hints::KEY_DPR) ?: 1;
  }

  /**
   * @return string
   */
  public function getEffectiveConnectionType()
  {
    return $this->getHeader(Hints::HEADER_ECT) ?: $this->getHint(Hints::KEY_ECT) ?: '4g';
  }

  /**
   * @return float|int
   */
  public function getHeight()
  {
    return $this->getHint(Hints::KEY_HEIGHT) ?: 1024;
  }

  /**
   * @return float|int
   */
  public function getViewportHeight()
  {
    return $this->getHint(Hints::KEY_VIEWPORT_HEIGHT) ?: 768;
  }

  /**
   * @return float|int
   */
  public function getViewportWidth()
  {
    return $this->getHeader(Hints::HEADER_VIEWPORT_WIDTH) ?: $this->getHint(Hints::KEY_VIEWPORT_WIDTH) ?: 1024;
  }

  /**
   * @return float|int
   */
  public function getWidth()
  {
    return $this->getHeader(Hints::HEADER_WIDTH) ?: $this->getHint(Hints::KEY_WIDTH) ?: 1024;
  }
}
