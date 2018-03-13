<?php

use Brain\Monkey\Functions;
use Brain\Monkey\WP\Actions;
use Tonik\Gin\Foundation\Autoloader;
use Tonik\Gin\Foundation\Config;
use Tonik\Gin\Foundation\Exception\FileNotFoundException;

class AutoloaderTest extends TestCase
{
    /**
     * @test
     */
    public function test_relative_path_getter()
    {
        $config = $this->getConfig();
        $autoloader = $this->getAutoloader($config);

        $this->assertEquals($autoloader->getRelativePath('file/to/load.php'), 'app/file/to/load.php');
    }

    /**
     * @test
     */
    public function test_path_getter()
    {
        $config = $this->getConfig();
        $autoloader = $this->getAutoloader($config);

        $this->assertEquals($autoloader->getPath('file/to/load.php'), 'abs/path/app/file/to/load.php');
    }

    /**
     * @test
     */
    public function it_should_throw_if_no_file()
    {
        $config = $this->getConfig();
        $autoloader = $this->getAutoloader($config);

        Functions::expect('locate_template')
            ->once()
            ->with('app/file/to/load.php', true, true)
            ->andReturn(false);

        $this->expectException(FileNotFoundException::class);

        $autoloader->register();
    }

    /**
     * @test
     */
    public function it_should_return_true_on_successfully_autoloading()
    {
        $config = $this->getConfig();
        $autoloader = $this->getAutoloader($config);

        Actions::expectFired('tonik/gin/autoloader/before_load')->once();
        Actions::expectFired('tonik/gin/autoloader/after_load')->once();

        Functions::expect('locate_template')
            ->once()
            ->with('app/file/to/load.php', true, true)
            ->andReturn(true);

        $autoloader->register();
    }

    public function getConfig()
    {
        return new Config([
            'paths' => [
                'directory' => 'abs/path',
            ],
            'directories' => [
                'app' => 'app'
            ],
            'autoload' => [
                'file/to/load.php',
            ]
        ]);
    }

    public function getAutoloader($config)
    {
        return new Autoloader($config);
    }
}