<?php

namespace Tonik\Gin\Foundation\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;

class CLI
{
    /**
     * Console application instance.
     *
     * @var \Symfony\Component\Console\Application
     */
    protected $app;

    /**
     * List of commands to register.
     *
     * @var array
     */
    protected $commands = [
        'Tonik\Gin\Foundation\Console\Command\ShakeCommand'
    ];

    /**
     * Console application banner.
     *
     * @var string
     */
    private $banner = '
    ___  __                 /  __
     |  /  \ |\ | | |__/   /  / _` | |\ |
     |  \__/ | \| | |  \  /   \__| | | \|
                         /
    ';

    /**
     * Construct CLI.
     */
    function __construct($dir)
    {
        $this->app = new Application($this->banner);

        $this->dir = $dir;

        $this->bootstrap();
    }

    /**
     * Boodstraps CLI.
     *
     * @return void
     */
    protected function bootstrap()
    {
        $this->addCommands($this->commands);

        $this->app->run();
    }

    /**
     * Registers commands within console application.
     *
     * @param array $commands
     */
    protected function addCommands(array $commands)
    {
        foreach ($commands as $name) {
            $command = new $name;

            $command->addOption(
                'directory',
                'dir',
                InputOption::VALUE_REQUIRED,
                'Root directory path of theme.',
                $this->dir
            );

            $this->app->add($command);
        }
    }
}