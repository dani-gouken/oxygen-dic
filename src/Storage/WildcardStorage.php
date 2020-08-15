<?php


namespace Atom\DI\Storage;

use Atom\DI\Contracts\DefinitionContract;
use Atom\DI\Definitions\Wildcard;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Exceptions\UnsupportedInvokerException;
use Atom\DI\Extraction\WildcardExtractor;

class WildcardStorage extends AbstractStorage
{
    public const STORAGE_KEY = "WILDCARDS";
    protected $supportedExtractors = [WildcardExtractor::class];

    /**
     * @param string $key
     * @param Wildcard|DefinitionContract $value
     * @return mixed|void
     * @throws UnsupportedInvokerException
     */
    public function store(string $key, DefinitionContract $value)
    {
        if (!($value instanceof Wildcard)) {
            throw new UnsupportedInvokerException($this->getStorageKey(), $key, $value, $this->supportedExtractors);
        }
        if (!$value->hasPattern()) {
            $value->setPattern($key);
        }
        parent::store($key, $value);
    }

    public function has(string $key): bool
    {
        return $this->getMatchFor($key) != null;
    }

    private function getMatchFor(string $class): ?Wildcard
    {
         /**
         * @var Wildcard $description
         */
        foreach ($this->descriptions as $description) {
            if ($this->match($description->getPattern(), $class)) {
                return $description;
            }
        }
        return null;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws NotFoundException
     */
    public function resolve(string $key): DefinitionContract
    {
        $definition = $this->getMatchFor($key);
        if ($definition == null) {
            throw new NotFoundException($key, $this);
        }
        $definition->setClass($key);
        return $definition;
    }

    private function match(string $pattern, string $source):bool
    {
        $pattern = preg_quote($pattern, '/');
        $pattern = '/^' . str_replace('\*', '.*', $pattern). '$/';
        return preg_match($pattern, $source);
    }

    /**
     * @param string $pattern
     * @param string $replacement
     * @return mixed|void
     * @throws UnsupportedInvokerException
     */
    public function add(string $pattern, string $replacement)
    {
        return $this->store($pattern, new Wildcard($replacement, $pattern));
    }

    /**
     * @inheritDoc
     */
    public function getStorageKey(): string
    {
        return self::STORAGE_KEY;
    }
}
