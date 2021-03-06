<?php
namespace Xpressengine\Tests\Plugin;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Mockery;
use Xpressengine\Plugin\PluginCollection;
use Xpressengine\Plugin\PluginHandler;
use Xpressengine\Plugin\PluginRegister;

class PluginHandlerTest extends \PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testConstruct()
    {
        $handler = $this->getHandler();
        $this->assertInstanceOf('\Xpressengine\Plugin\PluginHandler', $handler);
    }

    public function testGetAllPlugins()
    {

        $handler = $this->getHandler();
        $this->assertInstanceOf(PluginCollection::class, $handler->getAllPlugins());

        /** @var Mockery\MockInterface $plugins */
        $plugins = $this->makeCollection();
        $plugins->shouldReceive('initialize')->with(true)->once()->andReturnNull();

        $handler = $this->getHandler(null, $plugins);
        $this->assertInstanceOf(PluginCollection::class, $handler->getAllPlugins(true));

    }

    public function testIsActivated()
    {
        $pluginId = 'plugin_sample';
        /** @var Mockery\MockInterface $plugins */
        $plugins = $this->makeCollection();
        $plugins->shouldReceive('get')->with($pluginId)->once()->andReturnNull();

        $handler = $this->getHandler(null, $plugins);
        $this->assertFalse($handler->isActivated($pluginId));

        /** @var Mockery\MockInterface $plugins */
        $entity = $this->makeEntity();
        $entity->shouldReceive('isActivated')->once()->withNoArgs()->andReturn(true);

        $plugins = $this->makeCollection();
        $plugins->shouldReceive('get')->with($pluginId)->once()->andReturn($entity);

        $handler = $this->getHandler(null, $plugins);
        $this->assertTrue($handler->isActivated($pluginId));

    }

    /**
     * @expectedException \Xpressengine\Plugin\Exceptions\PluginNotFoundException
     *
     * @return void
     */
    public function testActivatePluginFailIfNoEntityFound()
    {
        $pluginId = 'plugin_sample';
        $plugins = $this->makeCollection();
        $plugins->shouldReceive('get')->with($pluginId)->once()->andReturnNull();

        $handler = $this->getHandler(null, $plugins);

        $handler->activatePlugin($pluginId);
    }

    /**
     * @expectedException \Xpressengine\Plugin\Exceptions\PluginAlreadyActivatedException
     *
     * @return void
     */
    public function testActivatePluginFailIfEntityWasActivated()
    {
        $pluginId = 'plugin_sample';
        $plugins = $this->makeCollection();

        $entity = $this->makeEntity();
        $entity->shouldReceive('getStatus')->once()->withNoArgs()->andReturn(PluginHandler::STATUS_ACTIVATED);

        $plugins->shouldReceive('get')->with($pluginId)->once()->andReturn($entity);

        $handler = $this->getHandler(null, $plugins);

        $handler->activatePlugin($pluginId);
    }

    /**
     *
     * @return void
     */
    public function testActivatePluginSuccess()
    {
        $pluginId = 'plugin_sample';
        $plugins = $this->makeCollection();

        $plugin = Mockery::mock('\Xpressengine\Plugin\AbstractPlugin');
        $plugin->shouldReceive('activate')->once()->andReturnNull();

        $entity = $this->makeEntity();
        $entity->shouldReceive('getStatus')->once()->withNoArgs()->andReturn(PluginHandler::STATUS_DEACTIVATED);
        $entity->shouldReceive('setStatus')->once()->withArgs(['activated'])->andReturnNull();
        $entity->shouldReceive('getObject')->once()->withNoArgs()->andReturn($plugin);
        $entity->shouldReceive('getVersion')->once()->withNoArgs()->andReturn('1.0');
        $entity->shouldReceive('getInstalledVersion')->once()->withNoArgs()->andReturn('0.9');
        $entity->shouldReceive('checkInstall')->once()->withNoArgs()->andReturn(true);
        $entity->shouldReceive('checkUpdate')->once()->withNoArgs()->andReturn(true);

        $plugins->shouldReceive('get')->with($pluginId)->once()->andReturn($entity);

        $handler = $this->getHandler(null, $plugins);
        $config = $this->setConfig($handler);
        $config->shouldReceive('getVal')->with('plugin.list', [])->once()->andReturn([
           $pluginId => []
        ]);
        $config->shouldReceive('setVal')->withAnyArgs()->once()->andReturnNull();

        $handler->activatePlugin($pluginId);
    }

    /**
     * @expectedException \Xpressengine\Plugin\Exceptions\PluginAlreadyDeactivatedException
     *
     * @return void
     */
    public function testDeactivatePluginFailIfEntityWasNotActivated()
    {
        $pluginId = 'plugin_sample';
        $plugins = $this->makeCollection();

        $entity = $this->makeEntity();
        $entity->shouldReceive('getStatus')->once()->withNoArgs()->andReturn(PluginHandler::STATUS_DEACTIVATED);

        $plugins->shouldReceive('get')->with($pluginId)->once()->andReturn($entity);

        $handler = $this->getHandler(null, $plugins);

        $handler->deactivatePlugin($pluginId);
    }

    /**
     *
     * @return void
     */
    public function testDeactivatePluginSuccess()
    {
        $pluginId = 'plugin_sample';
        $plugins = $this->makeCollection();

        $plugin = Mockery::mock('\Xpressengine\Plugin\AbstractPlugin');
        $plugin->shouldReceive('deactivate')->once()->andReturnNull();

        $entity = $this->makeEntity();
        $entity->shouldReceive('getStatus')->once()->withNoArgs()->andReturn(PluginHandler::STATUS_ACTIVATED);
        $entity->shouldReceive('setStatus')->once()->withArgs(['deactivated'])->andReturn();
        $entity->shouldReceive('getDependencies')->once()->withNoArgs()->andReturn([]);
        $entity->shouldReceive('getObject')->once()->withNoArgs()->andReturn($plugin);
        $entity->shouldReceive('getVersion')->once()->withNoArgs()->andReturn('1.0');

        $plugins->shouldReceive('get')->with($pluginId)->once()->andReturn($entity);
        $plugins->shouldReceive('fetchByStatus')->with(PluginHandler::STATUS_ACTIVATED)->once()->andReturn([$entity]);

        $handler = $this->getHandler(null, $plugins);
        $config = $this->setConfig($handler);
        $config->shouldReceive('getVal')->with('plugin.list', [])->once()->andReturn([
           $pluginId => []
        ]);
        $config->shouldReceive('setVal')->withAnyArgs()->once()->andReturnNull();

        $handler->deactivatePlugin($pluginId);
    }

    /**
     * makeCollection
     *
     * @return PluginCollection
     */
    private function makeCollection()
    {
        return Mockery::mock('\Xpressengine\Plugin\PluginCollection');
    }

    /**
     * @return Factory
     */
    private function makeViewFactory()
    {
        return Mockery::mock('\Illuminate\View\Factory', [
            'addNamespace' => null
        ]);

    }

    /**
     * makeRegister
     *
     * @return PluginRegister
     */
    private function makeRegister()
    {
        return Mockery::mock('\Xpressengine\Plugin\PluginRegister', [
            'addByEntity' => null
        ]);
    }

    /**
     * @return Application
     */
    private function makeApp()
    {
        return Mockery::mock('\Illuminate\Foundation\Application', [
            'singleton' => null
        ]);
    }

    private function makeEntity()
    {
        return Mockery::mock('\Xpressengine\Plugin\PluginEntity');
    }

    private function setConfig($handler)
    {

        $config = Mockery::mock('\Xpressengine\Config\ConfigManager');
        $handler->setConfig($config);
        return $config;
    }

    private function getHandler($dir = null, $plugins = null, $factory = null, $register = null, $app = null)
    {
        if($dir === null) $dir = __DIR__.'/plugins';
        if($plugins === null) $plugins = $this->makeCollection();
        if($factory === null) $factory = $this->makeViewFactory();
        if($register === null) $register = $this->makeRegister();
        if($app === null) $app = $this->makeApp();
        return new PluginHandler($dir, $plugins, $factory, $register, $app);
    }
}
