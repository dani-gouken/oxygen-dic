<?php


namespace Oxygen\DI\Extraction;

use Oxygen\DI\Exceptions\CircularDependencyException;

class ExtractionChain
{
    /**
     * @var string[]
     */
    public $chain = [];

    /**
     * @param string $item
     * @throws CircularDependencyException
     */
    public function append(string $item)
    {
        if ($this->contains($item)) {
            throw new CircularDependencyException($item, $this);
        }
        $this->chain[] = $item;
    }

    public function contains(string $item)
    {
        return in_array($item, $this->chain);
    }


    public function restartWith(string $item)
    {
        $this->clear();
        //$this->chain[$item] = $item;
    }


    public function clear()
    {
        $this->chain = [];
    }

    /**
     * @return array
     */
    public function getChain(): array
    {
        return $this->chain;
    }

    public function __toString()
    {
        return implode(" => ", $this->chain);
    }
}
