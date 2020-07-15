<?php


namespace Oxygen\DI\Extraction;

use Oxygen\DI\Contracts\ExtractionParameterContract;
use Oxygen\DI\Contracts\ExtractorContract;
use Oxygen\DI\DIC;
use Oxygen\DI\Exceptions\CircularDependencyException;
use Oxygen\DI\Exceptions\ContainerException;
use Oxygen\DI\Exceptions\NotFoundException;
use Oxygen\DI\Extraction\ExtractionParameters\FunctionExtractionParameter;
use ReflectionException;
use ReflectionFunction;

class FunctionExtractor implements ExtractorContract
{
    use ParameterResolverTrait;

    /**
     * @param ExtractionParameterContract $params
     * @param DIC $container
     * @return mixed
     * @throws ContainerException
     * @throws ReflectionException
     * @throws CircularDependencyException
     * @throws NotFoundException
     */
    public function extract(ExtractionParameterContract $params, DIC $container)
    {
        /**
         * @var $params FunctionExtractionParameter
         */
        $reflectedFunction = new ReflectionFunction($params->getMethod());
        $closure = $reflectedFunction->getClosure();
        $params = $this->getFunctionParameters($reflectedFunction, $container, $params, $params->getParameters());
        return call_user_func_array($closure, $params);
    }

    /**
     * @param $params
     * @return bool
     */
    public function isValidExtractionParameter(ExtractionParameterContract $params)
    {
        return $params instanceof FunctionExtractionParameter;
    }
}
