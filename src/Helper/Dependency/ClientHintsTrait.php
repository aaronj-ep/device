<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\Hints\ClientHints;

trait ClientHintsTrait
{
  /** @var ClientHints */
  protected $_ClientHints = null;

  /**
   * @return ClientHints
   */
  public function getClientHints(): ClientHints
  {
    if (!isset($this->_ClientHints))
    {
      $this->_ClientHints = new ClientHints();
    }

    return $this->_ClientHints;
  }

  /**
   * @param ClientHints $clientHints
   *
   * @return $this
   */
  public function setClientHints(ClientHints $clientHints)
  {
    $this->_ClientHints = $clientHints;

    return $this;
  }
}