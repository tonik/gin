<?php

use Brain\Monkey\Functions;
use Brain\Monkey\WP\Actions;
use Tonik\Gin\Foundation\Config;
use Tonik\Gin\Template\Template;
use Tonik\Gin\Foundation\Exception\FileNotFoundException;

class TemplateTest extends TestCase
{
    /**
     * @test
     */
    public function test_file_setter_and_getter()
    {
        $config = $this->getConfig();
        $template = $this->getTemplate($config, 'sample_template');

        $this->assertEquals($template->getFile(), 'sample_template');
    }

    /**
     * @test
     */
    public function test_absolute_path_getter()
    {
        $config = $this->getConfig();
        $template = $this->getTemplate($config, 'sample_template');

        $this->assertEquals($template->getPath(), 'abs/path/resources/templates/sample_template.tpl.php');
    }

    /**
     * @test
     */
    public function test_relative_path_getter()
    {
        $config = $this->getConfig();
        $template = $this->getTemplate($config, 'sample_template');

        $this->assertEquals($template->getRelativePath(), 'resources/templates/sample_template.tpl.php');
    }

    /**
     * @test
     */
    public function it_should_throw_exception_on_render_if_file_is_no_located()
    {
        $config = $this->getConfig();
        $template = $this->getTemplate($config, 'sample_template');

        Functions::expect('locate_template')->once()->with('resources/templates/sample_template.tpl.php', false, false)->andReturn(false);

        $this->expectException(FileNotFoundException::class);

        $template->render();
    }

    /**
     * @test
     */
    public function it_should_return_no_named_filename_when_template_name_is_not_valid()
    {
        $config = $this->getConfig();

        $invalidNamedTemplate = $this->getTemplate($config, ['sample_template', false]);
        $this->assertFalse($invalidNamedTemplate->isNamed());
        $this->assertEquals('sample_template.php', $invalidNamedTemplate->getFilename());

        $invalidNamedTemplate = $this->getTemplate($config, ['sample_template', null]);
        $this->assertFalse($invalidNamedTemplate->isNamed());
        $this->assertEquals('sample_template.php', $invalidNamedTemplate->getFilename());
    }

    /**
     * @test
     */
    public function it_should_return_named_filename_when_template_name_is_valid()
    {
        $config = $this->getConfig();

        $namedTemplate = $this->getTemplate($config, ['sample_template', 'named']);
        $this->assertTrue($namedTemplate->isNamed());
        $this->assertEquals('sample_template-named.php', $namedTemplate->getFilename());

        $namedTemplate = $this->getTemplate($config, ['sample_template', 'true']);
        $this->assertTrue($namedTemplate->isNamed());
        $this->assertEquals('sample_template-true.php', $namedTemplate->getFilename());

        $namedTemplate = $this->getTemplate($config, ['sample_template', 'null']);
        $this->assertTrue($namedTemplate->isNamed());
        $this->assertEquals('sample_template-null.php', $namedTemplate->getFilename());
    }

    /**
     * @test
     */
    public function it_should_do_get_template_part_action_on_render_with_no_named_template()
    {
        $config = $this->getConfig();
        $template = $this->getTemplate($config, 'sample_template');

        Functions::expect('locate_template')->twice()->andReturn(true);
        Actions::expectFired('get_template_part_sample_template')->once()->with('sample_template', null);

        $template->render();
    }

    /**
     * @test
     */
    public function it_should_do_get_template_part_action_on_render_with_named_template()
    {
        $config = $this->getConfig();

        $template = $this->getTemplate($config, ['sample_template', 'named']);
        Functions::expect('locate_template')->twice()->andReturn(true);
        Actions::expectFired('get_template_part_sample_template')->once()->with('sample_template', 'named');
        $template->render();

        $template = $this->getTemplate($config, ['sample_template', false]);
        Functions::expect('locate_template')->twice()->andReturn(true);
        Actions::expectFired('get_template_part_sample_template')->once()->with('sample_template', false);
        $template->render();

        $template = $this->getTemplate($config, ['sample_template', null]);
        Functions::expect('locate_template')->twice()->andReturn(true);
        Actions::expectFired('get_template_part_sample_template')->once()->with('sample_template', null);
        $template->render();
    }

    /**
     * @test
     */
    public function it_should_set_up_context_to_the_query_var()
    {
        $config = $this->getConfig();
        $template = $this->getTemplate($config, ['sample_template', 'named']);

        Functions::expect('locate_template')->twice()->andReturn(true);
        Functions::expect('set_query_var')->once()->with('key1', 'value1')->andReturn(null);
        Functions::expect('set_query_var')->once()->with('key2', 'value2')->andReturn(null);

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