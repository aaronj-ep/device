<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Hints\ClientHints;

interface ClientHintsAwareInterface
{
  /**
   * @return ClientHints
   */
  public function getClientHints(): ClientHints;

  /**
   * @param ClientHints $clientHints
   *
   * @return $this
   */
  public function setClientHints(ClientHints $clientHints);
}