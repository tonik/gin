<?php

namespace Tonik\Gin\Foundation\Console;

use Tonik\CLI\CLI;
use Symfony\Component\Console\Application;

class Kernel
{
    /**
     * Console application banner.
     *
     * @var string
     */
    const BANNER = '
    ___  __                 /  __
     |  /  \ |\ | | |__/   /  / _` | |\ |
     |  \__/ | \| | |  \  /   \__| | | \|
                         /
    ';

    /**
     * Construct CLI Kernel.
     *
     * @param string $dir
     */
    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    public function boot(Application $app, CLI $cli)
    {
        $app->setName(self::BANNER);

        return $cli->boot($app, $this->dir);
    }
}