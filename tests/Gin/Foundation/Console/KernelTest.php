<?php

use Symfony\Component\Console\Application;
use Tonik\CLI\CLI;
use Tonik\Gin\Foundation\Console\Kernel;

class KernelTest extends TestCase
{
    /**
     * @test
     */
    public function test_kernel_bootstraping()
    {
        $kernel = $this->getKernel();

        $app = Mockery::mock(Application::class);
        $cli = Mockery::mock(CLI::class);

        $app->shouldReceive('setName')
            ->with(Kernel::BANNER)
            ->andReturn($app);

        $cli->shouldReceive('boot')
            ->with($app, 'dir/path')
            ->andReturn(true);

        $kernel->boot($app, $cli);
    }

    public function getKernel()
    {
        return new Kernel('dir/path');
    }
}