<?php

namespace Tonik\Gin\Foundation;

use Tonik\Gin\Foundation\Exception\FileNotFoundException;
use Tonik\Gin\Foundation\Theme;

class Autoloader
{
    /**
     * Theme config instance.
     *
     * @var array
     */
    protected $config;

    /**
     * Construct autoloader.
     *
     * @param \Tonik\Gin\Foundation\Theme $theme
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Autoload registered files.
     *
     * @throws \Tonik\Gin\Foundation\Exception\FileNotFoundException
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->config['autoload'] as $file) {
            if ( ! locate_template($this->getRelativePath($file), true, true)) {
                throw new FileNotFoundException("Autoloaded file [{$this->getPath($file)}] cannot be found. Please, check your autoloaded entries in `config/theme.php` file.");
            }
        }

        return true;
    }

    /**
     * Gets absolute file path.
     *
     * @param  string $file
     *
     * @return string
     */
    public function getPath($file)
    {
        $file = $this->getRelativePath($file);

        return $this->config['paths']['directory'] . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Gets file path within `theme` directory.
     *
     * @param  string $file
     *
     * @return string
     */
    public function getRelativePath($file)
    {
        return $this->config['directories']['src'] . DIRECTORY_SEPARATOR . $file;
    }
}
