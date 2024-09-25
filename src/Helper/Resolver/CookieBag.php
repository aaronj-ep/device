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
  /** @var string  */
  private $name;

  /**
   * @param string $name
   */
  public function __construct(string $name = 'CH')
  {
    $this->name = $name;
  }

  /**
   * @param string $headerName
   *
   * @return string
   */
  public static function key(string $headerName): string
  {
    return preg_replace('#([a-z])[a-z]*[-_\s]*#i', '$1', HeaderBag::key($headerName));
  }

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
      if (isset($_COOKIE[$this->name]))
      {
        if ($cookie = $this->decodeHints($_COOKIE[$this->name]))
        {
          $this->cookie = $cookie;
        }
      }
    }

    return $this->cookie[$key] ?? null;
  }

  /**
   * Decodes the given string or array into an array of hints, first decoding any JSON string, then  normalizing 0 and
   * 1 to boolean values.  Code is safely wrapped in a try/catch to prevent malformed JSON from causing an exception.
   *
   * @param string|array $str
   *
   * @return array|null
   */
  private function decodeHints($str = null)
  {
    $normalized = preg_replace('#([a-z]+):#', '"$1": ', sprintf('{%s}', $str));

    try
    {
      return json_decode($normalized, true);
    }
    catch (\Exception $e)
    {
      return null;
    }
  }
}
