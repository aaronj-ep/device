<?php

namespace DevCoding\Helper\Dependency;

use DevCoding\CodeObject\Object\ClassString;
use DevCoding\CodeObject\Object\Reflection\ReflectionMethodComment;

/**
 * Resolves the type that an 'AwareInterface' is designed to support.
 */
class AwareTypeResolver
{
  /**
   * @param string $interface
   *
   * @return string
   */
  public function resolve(string $interface): string
  {
    if (false === strpos($interface, 'AwareInterface'))
    {
      throw new \InvalidArgumentException(
          sprintf("%s may only be used for '*AwareInterface', where the * is an existing class.", $interface)
      );
    }

    try
    {
      return $this->resolveType(new \ReflectionMethod($interface, $this->getter($interface)));
    }
    catch(\ReflectionException $e)
    {
      if (!interface_exists($interface))
      {
        $msg = sprintf('The interface "%s" does not exist.', $interface);
      }
      else
      {
        $msg = sprintf('The interface "%s" must contain a method "%s".', $interface, $this->getter($interface));
      }

      throw new \LogicException($msg, 0, $e);
    }
  }

  public function setter(string $interface): string
  {
    return 'set' . $this->name($interface);
  }

  public function getter(string $interface): string
  {
    return 'get'.$this->name($interface);
  }

  protected function name(string $interface): string
  {
    return str_replace('AwareInterface', '', (new ClassString($interface))->getName());
  }

  /**
   * Alternate method of a return type for a Reflection Method, backwards compatible to PHP 7.0 by relying on PHPDocs
   * for the method. This method will throw a LogicException if the return type is a builtin or nullable.
   *
   * @param \ReflectionMethod $method
   *
   * @throws \LogicException If the return type is a builtin, nullable, not present, or under PHP 7.0, not documented.
   * @return string
   */
  private function resolveType(\ReflectionMethod $method): string
  {
    $em = function(\ReflectionMethod $m, string $tmpl)
    {
      return sprintf($tmpl, $m->getDeclaringClass()->getName(), $m->getName());
    };

    $emBuiltIn = function(\ReflectionMethod $m) use ($em)
    {
      return $em($m, "%s::%s may not return a builtin type.");
    };

    $emNullable = function(\ReflectionMethod $m) use ($em)
    {
      return $em($m, "%s::%s may not be nullable.");
    };

    if (PHP_MAJOR_VERSION > 7 || (PHP_MAJOR_VERSION === 7 && PHP_MINOR_VERSION >= 1))
    {
      if ($refType = $method->getReturnType())
      {
        if ($refType->isBuiltin())
        {
          throw new \LogicException($emBuiltIn($method));
        }

        if ($refType->allowsNull())
        {
          throw new \LogicException($emNullable($method));
        }

        /** @noinspection ALL */
        return $refType->getName();
      }

      throw new \LogicException($em($method, "%s::%s must specify a return type."));
    }

    $comment = new ReflectionMethodComment($method);
    if ($return = $comment->getReturnType())
    {
      if ($comment->isBuiltIn())
      {
        throw new \LogicException($emBuiltIn($method));
      }

      if (!$comment->isNullable())
      {
        throw new \LogicException($emNullable($method));
      }

      if (is_string($return))
      {
        return $return;
      }
    }

    throw new \LogicException($em($method, "An @returns must be specified in the PHPDoc comment for %s::%s"));
  }
}