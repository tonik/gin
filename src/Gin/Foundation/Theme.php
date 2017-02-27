<?php

namespace Tonik\Gin\Foundation;

use Tonik\Gin\Foundation\Exception\BindingResolutionException;

class Theme extends Singleton
{
    /**
     * Theme registry.
     *
     * @var array
     */
    protected $registry = [];

    /**
     * Bind value into theme registry.
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return void
     */
    public function bind($key, $value)
    {
        $this->registry[$key] = $value;

        return $this;
    }

    /**
     * Resolve value from theme registry.
     *
     * @param  string $key
     * @param  array $parameters
     *
     * @return mixed
     */
    public function get($key, $parameters = [])
    {
        if (! isset($this->registry[$key])) {
            throw new BindingResolutionException("Unresolvable resolution. The [{$key}] binding is not registered.");
        }

        if (is_callable($abstract = $this->registry[$key])) {
            if (is_array($parameters)) {
                return call_user_func_array($abstract, $parameters);
            }

            return call_user_func($abstract, $parameters);
        }

        return $abstract;
    }
}
