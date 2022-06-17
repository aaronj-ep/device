<?php
/**
 * CookieResolver.php
 */

namespace DevCoding\Helper\Resolver;

/**
 * Contains methods to check for, decode, and provide hints from a cookie set by device.js
 *
 * Class CookieResolver
 *
 * @see     https://github.com/jonesiscoding/device
 *
 * @author  Aaron M Jones <am@jonesiscoding.com>
 * @licence MIT (https://github.com/jonesiscoding/device/blob/master/LICENSE)
 *
 * @package DevCoding\Resolver
 */
class CookieBag
{
  /** @var array */
  private $cookie;

  /**
   * @param $key_or_keys
   *
   * @return mixed|null
   */
  public function resolve($key_or_keys)
  {
    $arr = is_array($key_or_keys) ? $key_or_keys : [$key_or_keys];
    foreach ($arr as $key)
    {
      if ($val = $this->getCookieValue($key))
      {
        return $val;
      }
    }

    return null;
  }

  /**
   * @return bool
   */
  public function isHinted()
  {
    return isset($_COOKIE['djs']);
  }

  private function getCookieValue($key)
  {
    if (is_null($this->cookie))
    {
      $this->cookie = [];

      if (isset($_COOKIE['djs']))
      {
        $cookie = $this->decodeHints($_COOKIE['djs']);
        $rItit  = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($cookie));

        foreach ($rItit as $leafValue)
        {
          $keys = [];
          foreach (range(0, $rItit->getDepth()) as $depth)
          {
            $keys[] = $rItit->getSubIterator($depth)->key();
          }
          $this->cookie[join('.', $keys)] = $leafValue;
        }
      }
    }

    return $this->cookie[$key] ?? null;
  }

  /**
   * Decodes the given string or array into an array of hints, first decoding any JSON string, then  normalizing 0 and
   * 1 to boolean values.  Code is safely wrapped in a try/catch to prevent malformed JSON from causing an exception.
   *
   * @param string|array $json_or_array
   *
   * @return array|null
   */
  private function decodeHints($json_or_array = null)
  {
    $hints = (is_string($json_or_array)) ? json_decode($json_or_array, true) : $json_or_array;
    try
    {
      foreach ($hints as $key => $hint)
      {
        if (is_array($hint))
        {
          $hints[$key] = $this->decodeHints($hint);
        }
        elseif (0 == $hint)
        {
          $hints[$key] = false;
        }
        elseif (1 == $hint)
        {
          $hints[$key] = true;
        }
      }

      return $hints;
    }
    catch (\Exception $e)
    {
      return (is_array($json_or_array)) ? $json_or_array : null;
    }
  }
}
