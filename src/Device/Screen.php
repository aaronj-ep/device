<?php

namespace DevCoding\Device;

use DevCoding\Client\Object\Hardware\Pointers;
use DevCoding\Hints\Hint\DPR;
use DevCoding\Hints\Hint\Height;
use DevCoding\Hints\Hint\Width;
use DevCoding\Hints\Hint\Pointers as PointersHint;

class Screen extends DeviceChild
{
  /**
   * @return float|int
   */
  public function getDevicePixelRatio()
  {
    return $this->ClientHints->get(DPR::HEADER);
  }

  /**
   * @return float|int
   */
  public function getHeight()
  {
    return $this->ClientHints->get(Height::HEADER);
  }

  /**
   * @return Pointers
   */
  public function getPointers()
  {
    $pointers = $this->ClientHints->array(PointersHint::HEADER);
    if (!empty($pointers))
    {
      $primary = array_shift($pointers);

      return new Pointers($primary, $pointers);
    }

    return null;
  }

  /**
   * @return float|int
   */
  public function getWidth(): float
  {
    return $this->ClientHints->get(Width::HEADER);
  }
}
