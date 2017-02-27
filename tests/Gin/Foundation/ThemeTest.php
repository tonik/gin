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
    public function test_binding_and_resolving_value_from_the_theme_container()
    {
        $theme = Theme::getInstance();

        $theme->bind('key', 'value');

        $this->assertEquals($theme->get('key'), 'value');
    }

    /**
     * @test
     */
    public function test_binding_and_resolving_callable_from_the_theme_container()
    {
        $theme = Theme::getInstance();

        $theme->bind('callable.without.parameters', function () { return 'value'; });
        $theme->bind('callable.with.parameter', function ($param) { return $param; });
        $theme->bind('callable.with.parameters', function ($param1, $param2) { return $param1.','.$param2; });

        $this->assertEquals($theme->get('callable.without.parameters'), 'value');
        $this->assertEquals($theme->get('callable.with.parameter', 'value'), 'value');
        $this->assertEquals($theme->get('callable.with.parameters', ['value1', 'value2']), 'value1,value2');
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