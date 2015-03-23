<?php

namespace App\Src;

class WorkshopTest extends \TestCase
{

    /**
     * Don not consider those tests real.
     * They simply signify that everything is more-less ok.
     * For details just eye your output.
     */

    protected $item;

    public function setUp()
    {
        parent::setUp();
        $factory = new Factory();
        $path = '/home/vagrant/Code/DFParser/tests/raw/short/!test3.txt';
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
//
    /** @test **/
    public function it_processes_array_to_html_for_everything()
    {
        $this->assertTrue(is_array($this->item->name['array'][0]));
        $this->assertTrue(isset($this->item->color['html']));
        $this->assertTrue(isset($this->item->block['html']));
        $this->assertTrue(isset($this->item->build_item['html']));
        $this->assertTrue(isset($this->item->build_key['html']));
        $this->assertTrue(isset($this->item->build_labor['html']));
        $this->assertTrue(isset($this->item->dim['html']));
        $this->assertTrue(isset($this->item->name['html']));
        $this->assertTrue(isset($this->item->name_color['html']));

        $this->assertFalse(isset($this->item->needs_magma));
    }
}
