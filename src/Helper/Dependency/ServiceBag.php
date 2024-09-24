<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\CodeObject\Object\Reflection\ReflectionMethodComment;
use DevCoding\Helper\Resolver\CookieBag;

class ServiceBag extends DependencyBag
{
  /** @var callable[] */
  protected $factories;
  /** @var string[] */
  protected $loading;

  /**
   * @param object[] $services
   */
  public function __construct(array $services)
  {
    foreach($services as $id => $obj)
    {
      $class = is_numeric($id) ? get_class($obj) : $id;
      if (!class_exists($class))
      {
        throw new \InvalidArgumentException(
          sprintf("The class '%s' given to '%s' does not exist.", $id, get_class($this))
        );
      }

      if ($obj instanceof $class)
      {
        // Store the object if it matches the ID
        $this->objects[$class] = $obj;
      }
      elseif (is_callable($obj))
      {
        $this->factories[$class] = $obj;
      }
    }
  }

  /**
   * @param string|object $id
   *
   * @return $this
   * @throws \Exception
   */
  public function assert($id)
  {
    if (is_object($id))
    {
      $this->objects[get_class($id)] = $id;
    }
    elseif (!$this->has($id))
    {
      $this->loading[$id] = $id;

      if (!$factory = $this->factories[$id] ?? null)
      {
        $factory = function() use ($id) {
          if (class_exists($id))
          {
            $reflect = new \ReflectionClass($id);
            if ($method = $reflect->getConstructor())
            {
              $types = $this->getParameterTypes($method);
            }
            else
            {
              $types = [];
            }

            if (!empty($types))
            {
              $args = [];

              foreach($types as $type)
              {
                $args[] = $this->assert($type)->get($type);
              }

              return $reflect->newInstanceArgs($args);
            }
            else
            {
              return new $id();
            }
          }

          throw new \InvalidArgumentException(sprintf('The class %s does not exist', get_class($id)));
        };
      }

      $this->objects[$id] = $factory();

      unset($this->loading[$id]);
    }

    return $this;
  }

  protected function getParameterTypes(\ReflectionMethod $method)
  {
    $retval  = [];
    $params  = $method->getParameters();
    foreach($params as $param)
    {
      unset($best);
      $type  = $param->getType();
      $types = method_exists($type, 'getTypes') ? $type->getTypes() : [$type];

      foreach($types as $type)
      {
        if (!isset($best))
        {
          if (!isset($type) || !method_exists($type, 'getType'))
          {
            $comment = $comment ?? new ReflectionMethodComment($method);
            $cType   = $comment->getParamType($param->getName());
            $cType   = is_array($cType) ? reset($cType) : $cType;

            if (class_exists($cType))
            {
              $best = $cType;
            }
          }
          elseif (!$type->isBuiltin())
          {
            $best = $type->getName();
          }
        }
      }

      if (!isset($best) && !$param->isOptional() && !$param->isDefaultValueAvailable() && !$param->isVariadic())
      {
        throw new \LogicException(sprintf('Cannot automatically wire %s', $method->getDeclaringClass()->getName()));
      }
      elseif(isset($best))
      {
        $retval[] = $best;
      }
    }

    return $retval;
  }
}
