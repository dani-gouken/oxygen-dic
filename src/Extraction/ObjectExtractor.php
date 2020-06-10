<?php


namespace Oxygen\DI\Extraction;


use Exception;
use Oxygen\DI\Contracts\ExtractionParameterContract;
use Oxygen\DI\Contracts\ExtractorContract;
use Oxygen\DI\DIC;
use Oxygen\DI\Exceptions\CircularDependencyException;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Exceptions\StorageNotFoundException;
use Oxygen\DI\Extraction\ExtractionParameters\ObjectExtractionParameter;
use Oxygen\DI\Storage\ValueStorage;
use Oxygen\DI\Value;
use ReflectionClass;

class ObjectExtractor implements ExtractorContract
{
    use ParameterResolverTrait;

    /**
     * @param ExtractionParameterContract $params
     * @param DIC $container
     * @return object
     * @throws ContainerException
     * @throws StorageNotFoundException
     * @throws CircularDependencyException
     * @throws NotFoundException
     */
    public function extract(ExtractionParameterContract $params, DIC $container)
    {
        /**
         * @var $params ObjectExtractionParameter
         */
        $className = $params->getClassName();
        try {
            $reflectedClass = new ReflectionClass($params->getClassName());
        } catch (Exception $e) {
            throw new ContainerException("Unable to generate reflected class for [$className]");
        }
        if (!$reflectedClass->isInstantiable()) {
            throw new ContainerException("The class [$className] is not instantiable");
        }
        $constructor = $reflectedClass->getConstructor();
        if (is_null($constructor)) {
            return $reflectedClass->newInstance();
        }
        $constructor->getParameters();
        $resolvedParameters = $this->getFunctionParameters($constructor, $container,$params,$params->getConstructorArgs());
        $result = $reflectedClass->newInstanceArgs($resolvedParameters);
        if ($params->canCacheResult()) {
            $container->getStorage(ValueStorage::STORAGE_KEY)->store($className, new Value($result));
        }
        return $result;
    }

    public function isValidExtractionParameter(ExtractionParameterContract $params)
    {
        return $params instanceof ObjectExtractionParameter;
    }
}