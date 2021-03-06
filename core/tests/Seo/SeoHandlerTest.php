<?php
namespace Xpressengine\Tests\Seo;

use Mockery as m;
use Xpressengine\Seo\SeoHandler;

class SeoHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testImport()
    {
        list($importers, $setting, $translator) = $this->getMocks();
        $instance = m::mock(SeoHandler::class, [$importers, $setting, $translator])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $mockItem1 = m::mock('Xpressengine\Seo\SeoUsable');
        $mockItem2 = new \stdClass();
        $mockItem3 = new \stdClass();

        $instance->shouldReceive('resolveData')->once()->with($mockItem1)->andReturn([
            'type' => 'website',
            'siteName' => 'site name',
            'title' => 'site title'
        ]);

        foreach ($importers as $importer) {
            $importer->shouldReceive('exec')->once()->with([
                'type' => 'website',
                'siteName' => 'site name',
                'title' => 'site title'
            ]);
        }

        $instance->import([$mockItem1, $mockItem2, $mockItem3]);
    }

    public function testResolveData()
    {
        list($importers, $setting, $translator) = $this->getMocks();
        $instance = m::mock(SeoHandler::class, [$importers, $setting, $translator])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $mockItem = m::mock('Xpressengine\Seo\SeoUsable');
        $mockItem->shouldReceive('getUrl')->andReturn('http://someurl.com/path/name');
        $mockItem->shouldReceive('getDescription')->andReturn('sample description');
        $mockItem->shouldReceive('getKeyword')->andReturn(['test', 'sample']);
        $mockItem->shouldReceive('getAuthor')->andReturn('');
        $mockItem->shouldReceive('getImages')->andReturn([]);

        $instance->shouldReceive('makeTitle')->once()->with($mockItem)->andReturn('this is sparta!');

        $setting->shouldReceive('get')->once()->with('mainTitle')->andReturn('site name');
//        $setting->shouldReceive('get')->once()->with('description')->andReturn('');
//        $setting->shouldReceive('get')->once()->with('keywords')->andReturn('');

        $translator->shouldReceive('trans')->once()->with('site name')->andReturn('site name');

        $mockImage = m::mock('Xpressengine\Media\Spec\Image');
        $mockImage->shouldReceive('url')->once()->andReturn('/path/to/image');
        $setting->shouldReceive('getSiteImage')->once()->andReturn($mockImage);

        $data = $this->invokeMethod($instance, 'resolveData', [$mockItem]);
        
        $this->assertEquals('article', $data['type']);
        $this->assertEquals('site name', $data['siteName']);
        $this->assertEquals('http://someurl.com/path/name', $data['url']);
        $this->assertEquals('this is sparta!', $data['title']);
        $this->assertEquals('sample description', $data['description']);
        $this->assertEquals('test,sample', $data['keywords']);
        $this->assertFalse(isset($data['author']));
        $this->assertEquals(1, count($data['images']));
        $this->assertEquals('/path/to/image', reset($data['images']));
    }

    public function testMakeTitle()
    {
        list($importers, $setting, $translator) = $this->getMocks();
        $instance = new SeoHandler($importers, $setting, $translator);

        $mockItem = m::mock('Xpressengine\Seo\SeoUsable');
        $mockItem->shouldReceive('getTitle')->once()->andReturn('item title');

        $setting->shouldReceive('get')->twice()->with('mainTitle')->andReturn('main title');
        $setting->shouldReceive('get')->once()->with('subTitle')->andReturn('sub title');

        $translator->shouldReceive('trans')->twice()->with('main title')->andReturn('main title');
        $translator->shouldReceive('trans')->once()->with('sub title')->andReturn('sub title');

        $title = $this->invokeMethod($instance, 'makeTitle', [$mockItem]);
        $this->assertEquals('item title - main title', $title);

        $title = $this->invokeMethod($instance, 'makeTitle');
        $this->assertEquals('main title - sub title', $title);
    }

    private function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    private function getMocks()
    {
        return [
            [
                m::mock('Xpressengine\Seo\Importers\AbstractImporter')
            ],
            m::mock('Xpressengine\Seo\Setting'),
            m::mock('Xpressengine\Translation\Translator')
        ];
    }
}
