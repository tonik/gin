<?php

namespace Tonik\Gin\Foundation;

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
        if (is_callable($callable = $this->registry[$key])) {
            return call_user_func_array($callable, $parameters);
        }

        return $this->registry[$key];
    }
}
