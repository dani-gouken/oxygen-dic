<?php


namespace Atom\DI\Extraction;

use Atom\DI\Contracts\ExtractionParameterContract;
use Atom\DI\Contracts\ExtractorContract;
use Atom\DI\DIC;
use Atom\DI\Exceptions\CircularDependencyException;
use Atom\DI\Exceptions\ContainerException;
use Atom\DI\Exceptions\NotFoundException;
use Atom\DI\Extraction\ExtractionParameters\FunctionExtractionParameter;
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
         * @var FunctionExtractionParameter $params
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
