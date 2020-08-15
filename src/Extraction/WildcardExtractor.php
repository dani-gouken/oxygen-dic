<?php


namespace Atom\DI\Extraction;

use Atom\DI\Contracts\ExtractionParameterContract;
use Atom\DI\Contracts\ExtractorContract;
use Atom\DI\DIC;
use Atom\DI\Exceptions\CircularDependencyException;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Exceptions\StorageNotFoundException;
use Atom\DI\Extraction\ExtractionParameters\WildcardExtractionParameter;

class WildcardExtractor implements ExtractorContract
{

    /**
     * @param WildcardExtractionParameter|ExtractionParameterContract $params
     * @param DIC $container
     * @return mixed|void
     * @throws CircularDependencyException
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     */
    public function extract(ExtractionParameterContract $params, DIC $container)
    {
        $replacements = explode("*", $params->getReplacement());
        $patterns = explode("*", $params->getPattern());
        $className = $params->getClassName();
        foreach ($patterns as $index => $pattern) {
            if (isset($replacements[$index])) {
                $className = str_replace($pattern, $replacements[$index], $className);
            }
        }
        return $container->get($className);
    }

    public function isValidExtractionParameter(ExtractionParameterContract $params)
    {
        return $params instanceof WildcardExtractionParameter;
    }
}
