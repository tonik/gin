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
    public function test_binding_and_resolving_value_from_theme_container()
    {
        $theme = Theme::getInstance();

        $theme->bind('key', 'value');

        $this->assertEquals(Theme::getInstance()->get('key'), 'value');
    }

    /**
     * @test
     */
    public function test_binding_and_resolving_closure_from_theme_container()
    {
        $theme = Theme::getInstance();

        $theme->bind('key.without.parameters', function () {
            return 'value';
        });

        $theme->bind('key.with.parameters', function ($param1, $param2) {
            return $param1.','.$param2;
        });

        $this->assertEquals(Theme::getInstance()->get('key.without.parameters'), 'value');
        $this->assertEquals(Theme::getInstance()->get('key.with.parameters', ['value1', 'value2']), 'value1,value2');
    }
}