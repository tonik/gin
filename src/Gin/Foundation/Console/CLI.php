<?php

namespace Tonik\Gin\Foundation\Console;

use Symfony\Component\Console\Application;

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
    function __construct()
    {
        $this->app = new Application($this->banner);

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
        foreach ($commands as $command) {
            $this->app->add(new $command);
        }
    }
}