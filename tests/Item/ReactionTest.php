<?php

namespace App\Src;

class ReactionTest extends \TestCase
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
        $path = '/home/vagrant/Code/DFParser/tests/raw/short/reaction.txt';
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
        $this->assertTrue(is_array($this->item->name['array'][0]));
        $this->assertTrue(is_array($this->item->building));
        $this->assertTrue(is_array($this->item->reagent));
        $this->assertTrue(is_array($this->item->product));
        $this->assertTrue(is_array($this->item->improvement));
        $this->assertTrue(is_array($this->item->skill));
        $this->assertEquals(3, count($this->item->modifiers['array']));
    }
}
