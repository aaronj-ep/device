<?php

namespace DevCoding\Hints;

use DevCoding\Client\Object\Headers\UserAgentString;
use DevCoding\Helper\Dependency\ServiceBag;
use DevCoding\Helper\Resolver\ConfigBag;
use DevCoding\Helper\Resolver\CookieBag;
use DevCoding\Hints\Base\ArgumentAwareInterface;
use DevCoding\Hints\Base\BooleanValueInterface;
use DevCoding\Hints\Base\Hint;
use DevCoding\Hints\Base\BrowserHintInterface;
use DevCoding\Hints\Base\CookieHintInterface;
use DevCoding\Helper\Resolver\HeaderBag;
use DevCoding\Hints\Hint\ClientHint;
use DevCoding\Hints\Hint\FullVersionList;
use DevCoding\Hints\Hint\UserAgent;
use DevCoding\Client\Object\Browser\Browser;
use DevCoding\Client\Factory\BrowserFactory;

class ClientHints
{
  /** @var Hint[] */
  protected $hints;
  /** @var $ServiceBag */
  protected $container;

  /**
   * @param ConfigBag       $ConfigBag
   * @param HeaderBag|null  $HeaderBag
   * @param CookieBag|null  $CookieBag
   */
  public function __construct(ConfigBag $ConfigBag, $HeaderBag = null, $CookieBag = null)
  {
    $this->container = new ServiceBag(array_filter(func_get_args()));

    foreach($this->config()->get('hints') as $config)
    {
      $class = $config['class'] ?? ClientHint::class;

      if (!is_a($class, Hint::class, true))
      {
        throw new \InvalidArgumentException(sprintf('Class "%s" does not extend "%s".', $class, Hint::class));
      }

      // Instantiate the hint
      $hint = is_a($class, ArgumentAwareInterface::class, true) ? new $class($config) : new $class();
      // Add it to the hints array
      $this->hints[$hint->config()->header] = $hint;
    }
  }

  public function array($hint): array
  {
    $value = $this->get($hint);

    return isset($value) ? preg_split('#,\s*#', $value) : [];
  }

  /**
   * @param string $hint
   * @param bool|null $default
   *
   * @return bool|null
   */
  public function bool($hint, $default = null)
  {
    $value = $this->get($hint);
    if (isset($value))
    {
      $isFalse = in_array(strtolower($value), ['false', '?0', 'no-preference', 'no', 'off', '0', 'n']) ? false : null;
      $isTrue  = in_array(strtolower($value), ['true', '?1', 'reduce', 'yes', 'on', '1', 'y']) ? true : null;

      return $isTrue ?? $isFalse ?? $default;
    }

    return $default;
  }

  /**
   * @param string $header
   *
   * @return bool|float|int|mixed|string|null
   */
  public function get($header)
  {
    if ($hint = $this->hints[$header] ?? null)
    {
      return $this->resolve($hint);
    }

    return null;
  }

  public function warm(): ClientHints
  {
    $warmed = preg_split('#,\s?#', $_SERVER['CH_WARMED'] ?? '');

    foreach($this->hints as $resolver)
    {
      $head = $resolver->config->header;

      if (is_null($this->getHeaderBag()->resolve($head)))
      {
        $this->getHeaderBag()->populate([$head, $this->resolve($resolver)]);

        if (!$resolver instanceof CookieHintInterface || is_null($resolver->cookie($this->getCookieBag())))
        {
          $warmed[] = $head;
        }
      }
    }

    $_SERVER['CH_WARMED'] = implode(',', $warmed);

    return $this;
  }

  public function all()
  {
    $all = [];
    foreach($this->hints as $hint)
    {
      $all[$hint->config->header] = $this->resolve($hint);
    }

    return $all;
  }

  /**
   * @return Browser
   */
  public function browser(): Browser
  {
    if (!$this->container->has(Browser::class))
    {
      $headers = [FullVersionList::HEADER, UserAgent::HEADER];
      foreach (UserAgentString::HEADERS as $header)
      {
        $headers[] = str_replace('HTTP_', '', $header);
      }

      $header = $this->getHeaderBag()->resolve($headers);
      if ($header && $browser = BrowserFactory::fromConfig($this->config()->getBrowsers())->build($header))
      {
        $this->container->assert($browser);
      }
      else
      {
        // This really shouldn't happen, as the header list is exaustive.
        // That said, if we don't allow for the circumstance - errors are thrown.
        $this->container->assert(new Browser(['Chromium'], UA::DEFAULT_VERSION));
      }
    }

    return $this->container->get(Browser::class);
  }

  public function has($header)
  {
    return isset($this->hints[$header]);
  }

  /**
   * Evaluates if the current client is a "bot" using configured or preset bot patterns.
   *
   * @return bool
   */
  public function isBot(): bool
  {
    $agent = $this->get(UserAgent::HEADER);
    $bots  = $this->config()->getBots();

    if (!empty($bots))
    {
      foreach($bots as $pattern)
      {
        if (preg_match($pattern, $agent))
        {
          return true;
        }
      }
    }
    elseif((new UserAgentString($agent))->isBot())
    {
      return true;
    }

    return false;
  }

  public function isWarmed()
  {
    return isset($_SERVER['CH_WARMED']);
  }

  /**
   * Returns an array of mod_rewrite directives for warming client hint headers using either alternate headers or
   * cookie values
   *
   * @return array
   */
  public function mod_rewrite(): array
  {
    $mr = [];
    $cn = $this->config()->has('cookie') ? $this->config()->get('cookie') : 'CH';
    foreach($this->hints as $hint)
    {
      $h = HeaderBag::toServer($hint->config()->header);
      if ($hint->config()->offsetExists('alternates'))
      {
        foreach($hint->config()->alternates as $alternate)
        {
          $a = HeaderBag::toServer($alternate);

          $mr[] = '# '.$hint->config()->header.' from '.$alternate.'.';
          $mr[] = 'RewriteCond %{'.$h.'} ^$';
          $mr[] = 'RewriteCond %{'.$a.'} ^(.*)$';
          $mr[] = 'RewriteRule .* - [E='.$h.':%1]';
          $mr[] = '';
        }
      }

      if($hint instanceof CookieHintInterface)
      {
        $c = $hint->config()->cookie;

        $mr[] = '# '.$hint->config()->header.' from cookie.';
        $mr[] = 'RewriteCond %{'.$h.'} ^$';
        if ($hint instanceof ListValueInterface)
        {
          $mr[] = 'RewriteCond %{HTTP_COOKIE} '.$cn.'=.*'.$c.':\s?"([^"]+)",?';
        }
        elseif ($hint instanceof BooleanValueInterface)
        {
          $mr[] = 'RewriteCond %{HTTP_COOKIE} '.$cn.'=.*'.$c.':\s?([01]),?';
        }
        else
        {
          $mr[] = 'RewriteCond %{HTTP_COOKIE} '.$cn.'=.*'.$c.':\s?([^,;]+),?';
        }
        $mr[] = 'RewriteRule .* - [E='.$h.':%1]';
        $mr[] = '';
      }
    }

    return $mr;
  }

  /**
   * @return ConfigBag
   */
  protected function config(): ConfigBag
  {
    return $this->container->get(ConfigBag::class);
  }

  /**
   * @return HeaderBag
   */
  protected function getHeaderBag(): HeaderBag
  {
    return $this->container->assert(HeaderBag::class)->get(HeaderBag::class);
  }

  protected function getCookieBag(): CookieBag
  {
    return $this->container->assert(CookieBag::class)->get(CookieBag::class);
  }

  /**
   * @param Hint $hint
   *
   * @return bool|float|int|mixed|string
   */
  protected function resolve(Hint $hint)
  {
    return $hint->header($this->getHeaderBag())
              ?? ($hint instanceof CookieHintInterface ? $hint->cookie($this->getCookieBag()) : null)
              ?? ($hint instanceof BrowserHintInterface ? $hint->browser($this->browser()) : null)
              ?? $hint->default()
    ;
  }
}
