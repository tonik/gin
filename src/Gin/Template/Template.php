<?php

namespace Tonik\Gin\Template;

use Tonik\Gin\Foundation\Config;
use Tonik\Gin\Foundation\Exception\FileNotFoundException;
use Tonik\Gin\Foundation\Theme;

class Template
{
    /**
     * Theme config instance.
     *
     * @var array
     */
    protected $config;

    /**
     * File path to the template.
     *
     * @var string
     */
    protected $file;

    /**
     * Construct template.
     *
     * @param \Tonik\Gin\Foundation\Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Render template.
     *
     * @param  array $context
     * @throws \Tonik\Gin\Foundation\Exception\FileNotFoundException
     *
     * @return void
     */
    public function render(array $context = [])
    {
        if (locate_template($this->getRelativePath(), false, false)) {
            $this->setContext($context);
            $this->doActions();

            return locate_template($this->getRelativePath(), true, false);
        }

        throw new FileNotFoundException("Template file [{$this->getRelativePath()}] cannot be located.");
    }

    /**
     * Sets context dataset on query.
     *
     * @param array $context
     *
     * @return void
     */
    public function setContext(array $context)
    {
        foreach ($context as $key => $value) {
            set_query_var($key, $value);
        }
    }

    /**
     * Calls before including template actions.
     *
     * @return void
     */
    public function doActions()
    {
        if ($this->isNamed()) {
            list($slug, $name) = $this->file;

            do_action("get_template_part_{$slug}", $slug, $name);

            return;
        }

        do_action("get_template_part_{$this->file}", $this->file, null);
    }

    /**
     * Gets absolute path to the template.
     *
     * @return string
     */
    public function getPath()
    {
        $directory = $this->config['paths']['directory'];

        return $directory . DIRECTORY_SEPARATOR . $this->getRelativePath();
    }

    /**
     * Gets template path within `resources/templates` directory.
     *
     * @return string
     */
    public function getRelativePath()
    {
        $templates = $this->config['directories']['templates'];

        return $templates . DIRECTORY_SEPARATOR . $this->getFilename();
    }

    /**
     * Gets template name.
     *
     * @return string
     */
    public function getFilename($extension = '.php')
    {
        if ($this->isNamed()) {
            return join('-', $this->file) . $extension;
        }

        return "{$this->file}{$extension}";
    }

    /**
     * Checks if temlate has variant name.
     *
     * @return boolean
     */
    public function isNamed()
    {
        return is_array($this->file);
    }

    /**
     * Sets the file path to the template.
     *
     * @param string $file
     *
     * @return self
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Gets the File path to the template.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
}