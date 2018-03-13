<?php

namespace Tonik\Gin\Foundation;

use Tonik\Gin\Contract\ConfigInterface;
use Tonik\Gin\Foundation\Exception\FileNotFoundException;

class Autoloader
{
    /**
     * Theme config instance.
     *
     * @var \Tonik\Gin\Contract\ConfigInterface
     */
    protected $config;

    /**
     * Construct autoloader.
     *
     * @param \Tonik\Gin\Contract\ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
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
        do_action('tonik/gin/autoloader/before_load');

        $this->load();

        do_action('tonik/gin/autoloader/after_load');
    }

    /**
     * Localize and autoloads files.
     *
     * @return void
     */
    public function load()
    {
        foreach ($this->config['autoload'] as $file) {
            if ( ! locate_template($this->getRelativePath($file), true, true)) {
                throw new FileNotFoundException("Autoloaded file [{$this->getPath($file)}] cannot be found. Please, check your autoloaded entries in `config/app.php` file.");
            }
        }
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

        return $this->config['paths']['directory'] . '/' . $file;
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
        return $this->config['directories']['app'] . '/' . $file;
    }
}
