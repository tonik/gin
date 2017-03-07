<?php

use Brain\Monkey\Functions;
use Brain\Monkey\WP\Actions;
use Tonik\Gin\Foundation\Config;
use Tonik\Gin\Foundation\Exception\FileNotFoundException;
use Tonik\Gin\Template\Template;

class TemplateTest extends TestCase
{
    /**
     * @test
     */
    public function test_file_setter_and_getter()
    {
        $config = $this->getConfig();
        $template = $this->getTemplate($config, 'sample-template');

        $this->assertEquals($template->getFile(), 'sample-template');
    }

    /**
     * @test
     */
    public function test_absolute_path_getter()
    {
        $config = $this->getConfig();
        $template = $this->getTemplate($config, 'sample-template');

        $this->assertEquals($template->getPath(), 'abs/path/resources/templates/sample-template.tpl.php');
    }

    /**
     * @test
     */
    public function test_relative_path_getter()
    {
        $config = $this->getConfig();
        $template = $this->getTemplate($config, 'sample-template');

        $this->assertEquals($template->getRelativePath(), 'resources/templates/sample-template.tpl.php');
    }

    /**
     * @test
     */
    public function it_should_throw_exception_on_render_if_file_is_no_located()
    {
        $config = $this->getConfig();
        $template = $this->getTemplate($config, 'sample-template');

        Functions::expect('locate_template')
            ->once()
            ->with('resources/templates/sample-template.tpl.php', false, false)
            ->andReturn(false);

        $this->expectException(FileNotFoundException::class);

        $template->render();
    }

    /**
     * @test
     */
    public function it_should_do_get_template_part_action_on_render_with_no_named_template()
    {
        $config = $this->getConfig();
        $template = $this->getTemplate($config, 'sample-template');

        Functions::expect('locate_template')
            ->twice()
            ->andReturn(true);

        Actions::expectFired('get_template_part_sample-template')
            ->once()
            ->with('sample-template', null);

        $template->render();
    }

    /**
     * @test
     */
    public function it_should_do_get_template_part_action_on_render_with_named_template()
    {
        $config = $this->getConfig();
        $template = $this->getTemplate($config, ['sample-template', 'named']);

        Functions::expect('locate_template')
            ->twice()
            ->andReturn(true);

        Actions::expectFired('get_template_part_sample-template')
            ->once()
            ->with('sample-template', 'named');

        $template->render();
    }

    /**
     * @test
     */
    public function it_should_set_up_context_to_the_query_var()
    {
        $config = $this->getConfig();
        $template = $this->getTemplate($config, ['sample-template', 'named']);

        Functions::expect('locate_template')
            ->twice()
            ->andReturn(true);

        Functions::expect('set_query_var')
            ->once()
            ->with('key1', 'value1')
            ->andReturn(null);

        Functions::expect('set_query_var')
            ->once()
            ->with('key2', 'value2')
            ->andReturn(null);

        $template->render([
            'key1' => 'value1',
            'key2' => 'value2'
        ]);
    }

    public function getConfig()
    {
        return new Config([
            'templates' => [
                'extension' => '.tpl.php'
            ],
            'paths' => [
                'directory' => 'abs/path',
            ],
            'directories' => [
                'templates' => 'resources/templates'
            ]
        ]);
    }

    public function getTemplate($config, $name)
    {
        return (new Template($config))->setFile($name);
    }
}