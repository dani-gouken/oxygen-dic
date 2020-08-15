<?php


namespace Atom\DI\Extraction;

use Atom\DI\Contracts\ExtractionParameterContract;
use Atom\DI\Contracts\ExtractorContract;
use Atom\DI\DIC;
use Atom\DI\Exceptions\CircularDependencyException;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Exceptions\StorageNotFoundException;
use Atom\DI\Extraction\ExtractionParameters\ContainerExtractionParameter;

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
         * @var ContainerExtractionParameter $params
         */
        return $container->getDependency($params->getExtractionKey(), null, [], false);
    }

    public function isValidExtractionParameter(ExtractionParameterContract $params)
    {
        return $params instanceof ContainerExtractionParameter;
    }
}
