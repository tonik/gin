<?php

namespace Tonik\Gin\Contract;

interface ConfigInterface
{
    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all();

    /**
     * Get the specified configuration value.
     *
     * @param  string  $key
     * @param  mixed   $default
     *
     * @return mixed
     */
    public function get($key, $default);

    /**
     * Set a given configuration value.
     *
     * @param  array|string  $key
     * @param  mixed   $value
     *
     * @return void
     */
    public function set($key, $value);

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function has($key);
}
