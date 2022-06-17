<?php

namespace DevCoding\Hints\Base;

trait GreaseTrait
{
  /**
   * @param false $full
   * @param int   $maxMajor
   *
   * @return string
   */
  protected function getBrands($full = false, $maxMajor = 96)
  {
    $bVer = $this->getGreaseVersion($full, $maxMajor);

    return sprintf(
        '"Chrome"; v="%s", %s, "Chromium"; v="%s"',
        $bVer,
        $this->getGrease($full, abs($bVer / 2)),
        $bVer
    );
  }

  /**
   * @param false $full
   * @param int   $maxMajor
   *
   * @return string
   */
  protected function getGrease($full = false, $maxMajor = 96)
  {
    $symbols = ['(', ')', ';', '#', ':', ' '];

    return sprintf(
        '%s%s%sBrowser"; v="%s"',
        array_rand($symbols),
        array_rand(['What', 'Cool', 'Not', 'Fun', 'Super', 'Your']),
        array_rand($symbols),
        $this->getGreaseVersion($full)
    );
  }

  /**
   * @param false $full
   * @param int   $maxMajor
   *
   * @return int|string
   */
  protected function getGreaseVersion($full = false, $maxMajor = 96)
  {
    return $full ? sprintf('%s.%s.%s', rand(1, $maxMajor), rand(1, 10), rand(1, 10)) : rand(1, $maxMajor);
  }
}
