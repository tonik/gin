<?php

namespace Tonik\Gin\Asset;

use Tonik\Gin\Foundation\Exception\FileNotFoundException;
use Tonik\Gin\Foundation\Theme;

class Asset
{
    /**
     * Theme config instance.
     *
     * @var array
     */
    protected $config;

    /**
     * Asset file.
     *
     * @var string
     */
    protected $file;

    /**
     * Construct asset.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get template file.
     *
     * @return string
     */
    public function getUri()
    {
        if ( ! $this->fileExists($file = $this->getPublicPath($this->file))) {
            throw new FileNotFoundException("Asset file [$file] cannot be located.");
        }

        return $this->getPublicUri($this->file);
    }

    /**
     * Gets asset uri path.
     *
     * @return string
     */
    public function getPublicUri()
    {
        $uri = $this->config['paths']['uri'];

        return $uri . DIRECTORY_SEPARATOR . $this->getRelativePath();
    }

    /**
     * Gets asset directory path.
     *
     * @return string
     */
    public function getPublicPath()
    {
        $directory = $this->config['paths']['directory'];

        return $directory . DIRECTORY_SEPARATOR . $this->getRelativePath();
    }

    /**
     * Gets asset relative path.
     *
     * @return string
     */
    public function getRelativePath()
    {
        $public = $this->config['directories']['public'];

        return $public . DIRECTORY_SEPARATOR . $this->file;
    }

    /**
     * Checks if asset file exsist.
     *
     * @param  string $file
     *
     * @return boolean
     */
    public function fileExists($file)
    {
        return file_exists($file);
    }

    /**
     * Sets the Asset file.
     *
     * @param string $file the file
     *
     * @return self
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }
}
