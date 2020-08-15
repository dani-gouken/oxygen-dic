<?php


namespace Atom\DI\Contracts;

use Nette\PhpGenerator\Method;

interface DefinitionContract
{
    public function getExtractorClassName(): string;

    public function getExtractionParameter(): ExtractionParameterContract;

    public function getResolutionCallback(): ?callable;
}
