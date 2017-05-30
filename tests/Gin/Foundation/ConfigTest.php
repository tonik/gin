<?php

use Brain\Monkey\WP\Filters;
use Tonik\Gin\Foundation\Config;

class ConfigTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_accept_array_of_items_on_construction()
    {
        $config = $this->getConfig();

        $this->assertEquals($config->all(), [
            'item1' => 'value1',
            'item2' => [
                'child' => 'value2'
            ]
        ]);
    }

    /**
     * @test
     */
    public function it_should_apply_filters_when_getting_values()
    {
        $config = $this->getConfig();

        Filters::expectApplied('tonik/gin/config/get/item1')->once()->with('value1')->andReturn('filtered_value');

        $this->assertEquals('filtered_value', $config->get('item1'));
    }

    /**
     * @test
     */
    public function it_should_apply_filters_when_setting_values()
    {
        $config = $this->getConfig();

        Filters::expectApplied('tonik/gin/config/set/item1')->once()->with('value')->andReturn('filtered_value');

        $config->set('item1', 'value');

        $this->assertEquals('filtered_value', $config->get('item1'));
    }

    /**
     * @test
     */
    public function test_items_getter()
    {
        $config = $this->getConfig();

        $this->assertEquals($config->get('item1'), 'value1');
        $this->assertEquals($config->get('non_exists_item', 'default_value'), 'default_value');
        $this->assertEquals($config['item2'], ['child' => 'value2']);
    }

    /**
     * @test
     */
    public function test_items_setter()
    {
        $config = $this->getConfig();

        $config['item1'] = 'new_value1';
        $config->set('item2', 'new_value2');
        $config->set([
            'item3' => 'value3',
            'item4' => 'value4',
        ]);

        $this->assertEquals($config->all(), [
            'item1' => 'new_value1',
            'item2' => 'new_value2',
            'item3' => 'value3',
            'item4' => 'value4',
        ]);
    }

    /**
     * @test
     */
    public function test_items_unsetter()
    {
        $config = $this->getConfig();

        unset($config['item1']);

        $this->assertFalse(isset($config['item1']));
    }

    public function getConfig()
    {
        return new Config([
            'item1' => 'value1',
            'item2' => [
                'child' => 'value2'
            ]
        ]);
    }
}