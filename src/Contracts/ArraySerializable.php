<?php


namespace Oxygen\DI\Contracts;


interface ArraySerializable
{
    public static function fromArray(array $array);

    public function toArray(): array;
}