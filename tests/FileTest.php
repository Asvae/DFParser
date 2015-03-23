<?php

namespace App\Src;

class FileTest extends \TestCase
{
    protected $file;

    public function setUp()
    {
        parent::setUp();
        $path = '/home/vagrant/Code/DFParser/tests/raw/short/MW/!test.txt';

        $this->file = new File($path);
    }

    public function tearDown()
    {
        unset($this->file);
    }

    /** @test **/
    public function it_is_initializable()
    {
        $this->assertInstanceOf('App\Src\File', $this->file);
    }

    /** @test */
    public function it_loads_text_from_file()
    {
        $this->file->loadText();
        $this->assertEquals('!NOFOO!][OBJECT:BODY]',$this->file->text);
    }

    /** @test */
    public function it_repairs_masterwork_raws()
    {
        $this->file
            ->loadText()
            ->masterworkRawFix();
        $this->assertEquals("YESFOO[][OBJECT:BODY]", $this->file->text);
    }

    /** @test */
    public function it_fetches_object()
    {
        $this->file->path = '/home/vagrant/Code/DFParser/tests/raw/short/!test1.txt';
        $this->file->loadText()
            ->masterworkRawFix()
            ->formArray();
        $this->assertEquals("BUILDING", $this->file->bObj['string']);
    }

    /** @test */
    public function it_splits_by_object()
    {
        $this->file->path = '/home/vagrant/Code/DFParser/tests/raw/short/!test2.txt';
        $this->file->loadText()
            ->masterworkRawFix()
            ->formArray()
            ->splitByObject();
        $this->assertEquals('BODY', $this->file->bObj['string']);
        $this->assertEquals(3, count($this->file->items));
    }
}
