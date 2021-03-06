<?php
/**
 *
 */
namespace Xpressengine\Tests\DynamicField;

use Mockery as m;
use PHPUnit_Framework_TestCase;
use Xpressengine\Config\ColumnEntity;

/**
 * Class ColumnEntityTest
 * @package Xpressengine\Tests\DynamicField
 */
class ColumnEntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * tear down
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * test column entity
     *
     * @return void
     */
    public function testColumnEntity()
    {
        $fluent = m::mock('Illuminate\Support\Fluent');
        $fluent->shouldReceive('nullable');
        $fluent->shouldReceive('unsigned');
        $fluent->shouldReceive('default');

        $table = m::mock('Illuminate\Database\Schema\Blueprint');
        $table->shouldReceive(\Xpressengine\DynamicField\ColumnDataType::STRING)->andReturn($fluent);
        $table->shouldReceive('dropColumn');

        $column = (new \Xpressengine\DynamicField\ColumnEntity(
            'data',
            \Xpressengine\DynamicField\ColumnDataType::STRING
        ))->setParams([255]);

        $column->setUnsigned();
        $column->setNullAble();
        $column->setDefault('');
        $column->setDescription('');
        $column->add($table);
        $column->drop($table);
    }


}
