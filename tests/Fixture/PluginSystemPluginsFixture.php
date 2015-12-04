<?php
namespace PluginSystem\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use PluginSystem\Model\Table\PluginSystemPluginsTable;

/**
 * PluginSystem\Model\Table\PluginSystemPluginsTable Test Case
 */
class PluginSystemPluginsTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.plugin_system.plugin_system_plugins'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('PluginSystemPlugins') ? [] : ['className' => 'PluginSystem\Model\Table\PluginSystemPluginsTable'];
        $this->PluginSystemPlugins = TableRegistry::get('PluginSystemPlugins', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PluginSystemPlugins);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
