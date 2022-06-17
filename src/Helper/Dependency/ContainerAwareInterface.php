<?php

namespace DevCoding\Helper\Dependency;

interface ContainerAwareInterface
{
  /**
   * @param string $id
   *
   * @return mixed|object
   */
  public function get($id);

  /**
   * @param ServiceBag $container
   *
   * @return $this
   */
  public function setContainer(ServiceBag $container);
}