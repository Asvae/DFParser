<?php

namespace App\Src;

class ItemTest extends \TestCase
{

    /**
     * Don not consider those tests real.
     * They only state everything is more-less OK.
     * For details just eye your output.
     */

    protected $item;

    public function setUp()
    {
        parent::setUp();
        $factory = new Factory();
        $path = '/home/vagrant/Code/DFParser/tests/raw/short/item_tool.txt';
        $file = $factory->newFile($path);
        $file->loadEverything();
        $this->item = $file->items[0];
        $this->item->process();
    }

    public function tearDown()
    {
        unset($this->item);
    }

    /** @test **/
    public function it_is_initializable()
    {
        $this->assertInstanceOf('App\Src\Item', $this->item);
    }

    /** @test **/
    public function it_processes_array_to_html_for_everything()
    {
        $this->assertTrue(is_array($this->item->item_name));
        $this->assertTrue(is_array($this->item->tile));
        $this->assertTrue(is_array($this->item->value));
        $this->assertEquals(1, count($this->item->modifiers['array']));
    }
}
