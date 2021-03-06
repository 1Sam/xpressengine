<?php
namespace Xpressengine\Tests\Plugin;
use Xpressengine\Plugin\AbstractPlugin;

class AbstractPluginTest /*extends \PHPUnit_Framework_TestCase*/
{

    /**
     * @var AbstractPlugin
     */
    protected $plugin;

    protected function tearDown()
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testGetPluginInfo()
    {
        $info = $this->plugin->getPluginInfo();

        $this->assertEquals(count($info), 6);
        $this->assertEquals($info['Name'], '플러그인명');
        $this->assertEquals($info['Version'], '3.0.2.2');
    }

    public function testGetPluginId()
    {
        $info = $this->plugin->getPluginId();

        $this->assertEquals($info, 'plugin_sample');
    }

    protected function setUp()
    {
        require_once __DIR__.'/plugins/plugin_sample/plugin.php';

        $this->plugin = new \Xpressengine\Tests\Plugin\Sample\PluginSample();

        parent::setUp(); // TODO: Change the autogenerated stub
    }
}
