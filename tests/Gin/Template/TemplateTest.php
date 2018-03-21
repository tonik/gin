<?php

use Brain\Monkey\Functions;
use Brain\Monkey\WP\Actions;
use Brain\Monkey\WP\Filters;
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
    public function test_filter_on_template_filename_getter()
    {
        $config = $this->getConfig();
        $template = $this->getTemplate($config, 'sample_template');

        Filters::expectApplied('tonik/gin/template/filename')->once()->with('sample_template.php')->andReturn('changed_template_name.php');

        $this->assertEquals('changed_template_name.php', $template->getFilename());
    }

    /**
     * @test
     */
    public function test_centext_filter_on_template_rendering()
    {
        $config = $this->getConfig();

        Functions::expect('locate_template')->andReturn($this->getFixtureTemplatePath());

        $template = $this->getTemplate($config, 'sample_template');
        Actions::expectFired('get_template_part_sample_template')->once()->with('sample_template', null);
        Filters::expectApplied('tonik/gin/template/context/sample_template.php')->once()->with(['key' => 'value'])->andReturn(['key' => 'changed']);
        ob_start();
        $template->render(['key' => 'value']);
        $this->assertEquals('<div>changed</div>', $this->removeNewLineAtEOF(ob_get_clean()));

        $template = $this->getTemplate($config, ['sample_template', 'named']);
        Actions::expectFired('get_template_part_sample_template')->once()->with('sample_template', 'named');
        Filters::expectApplied('tonik/gin/template/context/sample_template-named.php')->once()->with(['key' => 'value'])->andReturn(['key' => 'changed']);
        ob_start();
        $template->render(['key' => 'value']);
        $this->assertEquals('<div>changed</div>', $this->removeNewLineAtEOF(ob_get_clean()));
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

        $template->render(['key' => 'value']);
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

        $invalidNamedTemplate = $this->getTemplate($config, ['sample_template', '']);
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

        Functions::expect('locate_template')->once()->andReturn($this->getFixtureTemplatePath());
        Actions::expectFired('get_template_part_sample_template')->once()->with('sample_template', null);

        ob_start();
        $template->render(['key' => 'value']);
        ob_get_clean();
    }

    /**
     * @test
     */
    public function it_should_do_get_template_part_action_on_render_with_named_template()
    {
        $config = $this->getConfig();

        ob_start();

        $template = $this->getTemplate($config, ['sample_template', 'named']);
        Functions::expect('locate_template')->once()->andReturn($this->getFixtureTemplatePath());
        Actions::expectFired('get_template_part_sample_template')->once()->with('sample_template', 'named');
        $template->render(['key' => 'value']);

        $template = $this->getTemplate($config, ['sample_template', false]);
        Functions::expect('locate_template')->once()->andReturn($this->getFixtureTemplatePath());
        Actions::expectFired('get_template_part_sample_template')->once()->with('sample_template', false);
        $template->render(['key' => 'value']);

        $template = $this->getTemplate($config, ['sample_template', null]);
        Functions::expect('locate_template')->once()->andReturn($this->getFixtureTemplatePath());
        Actions::expectFired('get_template_part_sample_template')->once()->with('sample_template', null);
        $template->render(['key' => 'value']);

        ob_get_clean();
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

    public function getFixtureTemplatePath()
    {
        return dirname(__DIR__) . '/../fixtures/template.tpl.php';
    }

    public function removeNewLineAtEOF($string)
    {
        return trim(preg_replace('/\s\s+/', ' ', $string));
    }
}
