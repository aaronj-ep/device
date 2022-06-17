<?php

namespace DevCoding\Helper\Dependency;

use Psr\Container\ContainerInterface;

class DependencyBag implements ContainerInterface
{
  protected $objects;

  public function get(string $id)
  {
    return $this->objects[$id];
  }

  public function has(string $id): bool
  {
    return isset($this->objects[$id]);
  }
}
