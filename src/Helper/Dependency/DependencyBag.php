<?php

namespace DevCoding\Helper\Dependency;


class DependencyBag
{
  protected $objects;

  public function get($id)
  {
    return $this->objects[$id];
  }

  public function has($id)
  {
    return isset($this->objects[$id]);
  }
}
