<?php


namespace Oxygen\DI\Extraction;


use Oxygen\DI\Contracts\ExtractionParameterContract;
use Oxygen\DI\Contracts\ExtractorContract;
use Oxygen\DI\DIC;
use Oxygen\DI\Exceptions\CircularDependencyException;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Exceptions\StorageNotFoundException;
use Oxygen\DI\Extraction\ExtractionParameters\ContainerExtractionParameter;

class ContainerExtractor implements ExtractorContract
{
    /**
     * @param ExtractionParameterContract $params
     * @param DIC $container
     * @return mixed|void
     * @throws ContainerException
     * @throws NotFoundException
     * @throws StorageNotFoundException
     * @throws CircularDependencyException
     */
    public function extract(ExtractionParameterContract $params, DIC $container)
    {
        /**
         * @var $params ContainerExtractionParameter
         */
        return $container->getDependency($params->getKey());
    }

    public function isValidExtractionParameter(ExtractionParameterContract $params)
    {
        return $params instanceof ContainerExtractionParameter;
    }
}