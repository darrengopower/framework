<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-05 00:18
 */
namespace Notadd\Foundation\Translation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\NamespacedItemResolver;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\TranslatorInterface;
class Translator extends NamespacedItemResolver implements TranslatorInterface {
    /**
     * @var \Notadd\Foundation\Translation\LoaderInterface
     */
    protected $loader;
    /**
     * @var string
     */
    protected $locale;
    /**
     * @var string
     */
    protected $fallback;
    /**
     * @var array
     */
    protected $loaded = [];
    /**
     * @param  \Notadd\Foundation\Translation\LoaderInterface $loader
     * @param  string $locale
     */
    public function __construct(LoaderInterface $loader, $locale) {
        $this->loader = $loader;
        $this->locale = $locale;
    }
    /**
     * @param  string $key
     * @param  string $locale
     * @return bool
     */
    public function has($key, $locale = null) {
        return $this->get($key, [], $locale) !== $key;
    }
    /**
     * @param  string $key
     * @param  array $replace
     * @param  string $locale
     * @return string
     */
    public function get($key, array $replace = [], $locale = null) {
        list($namespace, $group, $item) = $this->parseKey($key);
        foreach($this->parseLocale($locale) as $locale) {
            $this->load($namespace, $group, $locale);
            $line = $this->getLine($namespace, $group, $locale, $item, $replace);
            if(!is_null($line)) {
                break;
            }
        }
        if(!isset($line)) {
            return $key;
        }
        return $line;
    }
    /**
     * @param  string $namespace
     * @param  string $group
     * @param  string $locale
     * @param  string $item
     * @param  array $replace
     * @return string|array|null
     */
    protected function getLine($namespace, $group, $locale, $item, array $replace) {
        $line = Arr::get($this->loaded[$namespace][$group][$locale], $item);
        if(is_string($line)) {
            return $this->makeReplacements($line, $replace);
        } elseif(is_array($line) && count($line) > 0) {
            return $line;
        }
    }
    /**
     * @param  string $line
     * @param  array $replace
     * @return string
     */
    protected function makeReplacements($line, array $replace) {
        $replace = $this->sortReplacements($replace);
        foreach($replace as $key => $value) {
            $line = str_replace(':' . $key, $value, $line);
        }
        return $line;
    }
    /**
     * @param  array $replace
     * @return array
     */
    protected function sortReplacements(array $replace) {
        return (new Collection($replace))->sortBy(function ($value, $key) {
            return mb_strlen($key) * -1;
        });
    }
    /**
     * @param  string $key
     * @param  int $number
     * @param  array $replace
     * @param  string $locale
     * @return string
     */
    public function choice($key, $number, array $replace = [], $locale = null) {
        $line = $this->get($key, $replace, $locale = $locale ?: $this->locale ?: $this->fallback);
        $replace['count'] = $number;
        return $this->makeReplacements($this->getSelector()->choose($line, $number, $locale), $replace);
    }
    /**
     * @param  string $id
     * @param  array $parameters
     * @param  string $domain
     * @param  string $locale
     * @return string
     */
    public function trans($id, array $parameters = [], $domain = 'messages', $locale = null) {
        return $this->get($id, $parameters, $locale);
    }
    /**
     * @param  string $id
     * @param  int $number
     * @param  array $parameters
     * @param  string $domain
     * @param  string $locale
     * @return string
     */
    public function transChoice($id, $number, array $parameters = [], $domain = 'messages', $locale = null) {
        return $this->choice($id, $number, $parameters, $locale);
    }
    /**
     * @param  string $namespace
     * @param  string $group
     * @param  string $locale
     * @return void
     */
    public function load($namespace, $group, $locale) {
        if($this->isLoaded($namespace, $group, $locale)) {
            return;
        }
        $lines = $this->loader->load($locale, $group, $namespace);
        $this->loaded[$namespace][$group][$locale] = $lines;
    }
    /**
     * @param  string $namespace
     * @param  string $group
     * @param  string $locale
     * @return bool
     */
    protected function isLoaded($namespace, $group, $locale) {
        return isset($this->loaded[$namespace][$group][$locale]);
    }
    /**
     * @param  string $namespace
     * @param  string $hint
     * @return void
     */
    public function addNamespace($namespace, $hint) {
        $this->loader->addNamespace($namespace, $hint);
    }
    /**
     * @param  string $key
     * @return array
     */
    public function parseKey($key) {
        $segments = parent::parseKey($key);
        if(is_null($segments[0])) {
            $segments[0] = '*';
        }
        return $segments;
    }
    /**
     * @param  string $locale
     * @return array
     */
    protected function parseLocale($locale) {
        if(!is_null($locale)) {
            return array_filter([
                $locale,
                $this->fallback
            ]);
        }
        return array_filter([
            $this->locale,
            $this->fallback
        ]);
    }
    /**
     * @return \Symfony\Component\Translation\MessageSelector
     */
    public function getSelector() {
        if(!isset($this->selector)) {
            $this->selector = new MessageSelector;
        }
        return $this->selector;
    }
    /**
     * @param  \Symfony\Component\Translation\MessageSelector $selector
     * @return void
     */
    public function setSelector(MessageSelector $selector) {
        $this->selector = $selector;
    }
    /**
     * @return \Notadd\Foundation\Translation\LoaderInterface
     */
    public function getLoader() {
        return $this->loader;
    }
    /**
     * @return string
     */
    public function locale() {
        return $this->getLocale();
    }
    /**
     * @return string
     */
    public function getLocale() {
        return $this->locale;
    }
    /**
     * @param  string $locale
     * @return void
     */
    public function setLocale($locale) {
        $this->locale = $locale;
    }
    /**
     * @return string
     */
    public function getFallback() {
        return $this->fallback;
    }
    /**
     * @param  string $fallback
     * @return void
     */
    public function setFallback($fallback) {
        $this->fallback = $fallback;
    }
}