<?php

use Brain\Monkey\Functions;
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

        $config->shouldReceive('offsetGet')
            ->with('directories')
            ->andReturn(['src' => 'src']);

        $this->assertEquals($autoloader->getRelativePath('file/to/load.php'), 'src/file/to/load.php');
    }

    /**
     * @test
     */
    public function test_path_getter()
    {
        $config = $this->getConfig();
        $autoloader = $this->getAutoloader($config);

        $config->shouldReceive('offsetGet')
            ->with('directories')
            ->andReturn(['src' => 'src']);

        $config->shouldReceive('offsetGet')
            ->with('paths')
            ->andReturn(['directory' => 'abs/path']);

        $this->assertEquals($autoloader->getPath('file/to/load.php'), 'abs/path/src/file/to/load.php');
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
            ->with('src/file/to/load.php', true, true)
            ->andReturn(false);

        $this->expectException(FileNotFoundException::class);

        $config->shouldReceive('offsetGet')
            ->with('directories')
            ->andReturn(['src' => 'src']);

        $config->shouldReceive('offsetGet')
            ->with('paths')
            ->andReturn(['directory' => 'abs/path']);

        $config->shouldReceive('offsetGet')
            ->with('autoload')
            ->andReturn(['file/to/load.php']);

        $autoloader->register();
    }

    /**
     * @test
     */
    public function it_should_return_true_on_successfully_autoloading()
    {
        $config = $this->getConfig();
        $autoloader = $this->getAutoloader($config);

        Functions::expect('locate_template')
            ->once()
            ->with('src/file/to/load.php', true, true)
            ->andReturn(true);

        $config->shouldReceive('offsetGet')
            ->with('directories')
            ->andReturn(['src' => 'src']);

        $config->shouldReceive('offsetGet')
            ->with('paths')
            ->andReturn(['directory' => 'abs/path']);

        $config->shouldReceive('offsetGet')
            ->with('autoload')
            ->andReturn(['file/to/load.php']);

        $this->assertEquals($autoloader->register(), true);
    }

    public function getConfig()
    {
        return Mockery::mock(Config::class, [
            'paths' => [
                'directory' => 'abs/path',
            ],
            'directories' => [
                'src' => 'src'
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