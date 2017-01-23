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
    public function test_binding_and_resolving_from_theme_container()
    {
        $theme = Theme::getInstance();

        $theme->bind('key', 'value');

        $this->assertEquals(Theme::getInstance()->get('key'), 'value');
    }
}