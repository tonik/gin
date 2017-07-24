<?php

namespace Tonik\Gin\Foundation;

use ArrayAccess;
use Closure;
use Tonik\Gin\Foundation\Exception\BindingResolutionException;

class Theme extends Singleton implements ArrayAccess
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
     * Resolves service with parameters.
     *
     * @param  mixed $abstract
     * @param  array $parameters
     * @return mixed
     */
    protected function resolve($abstract, array $parameters)
    {
        return call_user_func_array($abstract, [$this, $parameters]);
    }

    /**
     * Resolve service form container.
     *
     * @param  string $key
     * @param  array  $parameters
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

        // Otherwise, look for service
        // in services collection.
        if (isset($this->services[$key])) {
            // Resolve service jf we don't have
            // a deposit for this service.
            if ( ! isset($this->deposit[$key])) {
                $this->deposit[$key] = $this->resolve($this->services[$key], $parameters);
            }

            return $this->deposit[$key];
        }

        throw new BindingResolutionException("Unresolvable resolution. The [{$key}] binding is not registered.");
    }

    /**
     * Determine if the given service exists.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->factories[$key]) || isset($this->services[$key]);
    }

    /**
     * Removes service from the container.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function forget($key)
    {
        unset($this->factories[$key], $this->services[$key]);
    }

    /**
     * Gets a service.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Sets a service.
     *
     * @param string $key
     * @param Closure $service
     *
     * @return void
     */
    public function offsetSet($key, $service)
    {
        if ( ! is_callable($service)) {
            throw new BindingResolutionException("Service definition [{$service}] is not an instance of Closure");
        }

        $this->bind($key, $service);
    }

    /**
     * Determine if the given service exists.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Unsets a service.
     *
     * @param  string  $key
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        return $this->forget($key);
    }
}
