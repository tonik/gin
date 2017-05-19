<?php

use Tonik\Gin\Foundation\Theme;

class ThemeTest extends TestCase
{
    /**
     * @test
     */
    public function it_return_same_instance_on_every_access()
    {
        $theme = Theme::getInstance();

        $this->assertEquals(Theme::getInstance(), $theme);
    }

    /**
     * @test
     */
    public function test_binding_and_resolving_service()
    {
        $theme = Theme::getInstance();

        $theme->bind('key', function () { return 'value'; });

        $this->assertEquals($theme->get('key'), 'value');
    }

    /**
     * @test
     */
    public function test_binding_and_resolving_service_with_params()
    {
        $theme = Theme::getInstance();

        $theme->bind('params', function ($theme, $params) { return $params; });

        $this->assertEquals(['name' => 'John'], $theme->get('params', ['name' => 'John']));
    }

    /**
     * @test
     */
    public function it_should_give_access_to_the_theme_container_instance()
    {
        $theme = Theme::getInstance();

        $theme->bind('theme', function ($t) { return $t; });

        $this->assertSame($theme, $theme->get('theme'));
    }

    /**
     * @test
     */
    public function it_should_return_same_object_on_binding()
    {
        $theme = Theme::getInstance();

        $theme->bind('singleton', function () { return new stdClass; });

        $this->assertSame($theme->get('singleton'), $theme->get('singleton'));
    }

    /**
     * @test
     */
    public function it_should_return_same_object_on_factory_binding()
    {
        $theme = Theme::getInstance();

        $theme->factory('factory', function () { return new stdClass; });

        $this->assertNotSame($theme->get('factory'), $theme->get('factory'));
    }

    /**
     * @test
     */
    public function it_should_throw_exception_on_resolving_nonexisting_binding()
    {
        $theme = Theme::getInstance();

        $this->expectException('Tonik\Gin\Foundation\Exception\BindingResolutionException');

        $theme->get('nonexsiting.binding');
    }
}