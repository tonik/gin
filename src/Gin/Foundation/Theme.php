<?php

namespace Tonik\Gin\Foundation;

use Closure;
use Tonik\Gin\Foundation\Exception\BindingResolutionException;

class Theme extends Singleton
{
    /**
     * Collection of services.
     *
     * @var array
     */
    protected $services = [];

    /**
     * Collection of services factories.
     *
     * @var array
     */
    protected $factories = [];

    /**
     * Registry of deposited services.
     *
     * @var array
     */
    protected $deposit = [];

    /**
     * Bind service into collection.
     *
     * @param  string $key
     * @param  Closure $service
     *
     * @return void
     */
    public function bind($key, Closure $service)
    {
        $this->services[$key] = $service;

        return $this;
    }

    /**
     * Bind factory into collection.
     *
     * @param  string $key
     * @param  \Closure $factory
     *
     * @return void
     */
    public function factory($key, Closure $factory)
    {
        $this->factories[$key] = $factory;

        return $this;
    }

    /**
     * Resolve service form container.
     *
     * @param  string $key
     * @param  array $parameters
     *
     * @return mixed
     */
    public function get($key, array $parameters = [])
    {
        // If service is a factory, we should always
        // return new instance of the service.
        if (isset($this->factories[$key])) {
            return $this->resolve($this->factories[$key], $parameters);
        }

        if (isset($this->services[$key])) {
            if (! isset($this->deposit[$key])) {
                $this->deposit[$key] = $this->resolve($this->services[$key], $parameters);
            }

            return $this->deposit[$key];
        }

        throw new BindingResolutionException("Unresolvable resolution. The [{$key}] binding is not registered.");
    }

    /**
     * Resolves service with parameters.
     *
     * @param  mixed $abstract
     * @param  array  $parameters
     * @return mixed
     */
    protected function resolve($abstract, array $parameters)
    {
         return call_user_func_array($abstract, [$this, $parameters]);
    }
}
