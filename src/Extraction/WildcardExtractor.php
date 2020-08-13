<?php


namespace Oxygen\DI\Extraction;

use Oxygen\DI\Contracts\ExtractionParameterContract;
use Oxygen\DI\Contracts\ExtractorContract;
use Oxygen\DI\DIC;
use Oxygen\DI\Exceptions\CircularDependencyException;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Exceptions\StorageNotFoundException;
use Oxygen\DI\Extraction\ExtractionParameters\WildcardExtractionParameter;

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
