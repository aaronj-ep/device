<?php

namespace DevCoding\Hints\Base;

use DevCoding\Hints\LegacyUserAgent;
use DevCoding\Client\Object\Headers\UserAgentString as UserAgentObject;

/**
 * Class UserAgentTrait
 * @package DevCoding\Hints\Base
 */
trait UserAgentTrait
{
  /** @var UserAgentObject */
  protected $_UserAgent;

  abstract protected function getHeaderBag();

  /**
   * @return UserAgentObject|null
   */
  public function getUserAgentObject()
  {
    if (!isset($this->_UserAgent))
    {
      $this->_UserAgent = (new LegacyUserAgent())->setHeaderBag($this->getHeaderBag())->getObject();
    }

    return $this->_UserAgent;
  }

  /**
   * @param UserAgentObject $UserAgent
   *
   * @return $this
   */
  public function setUserAgent(UserAgentObject $UserAgent)
  {
    $this->_UserAgent = $UserAgent;

    return $this;
  }
}