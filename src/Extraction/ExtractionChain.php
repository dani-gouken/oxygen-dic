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
    public function append(string $item){
        if(in_array($item,$this->chain)){
            throw new CircularDependencyException($item,$this);
        }
        $this->chain[] = $item;
    }


    public function restartWith(string $item){
        $this->clear();
        //$this->chain[$item] = $item;
    }


    public function clear(){
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
        return implode(" => ",$this->chain);
    }


}