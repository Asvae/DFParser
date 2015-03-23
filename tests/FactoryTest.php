<?php

namespace App\Src;

class FactoryTest extends \TestCase {

    protected $factory;

    public function setUp()
    {
        parent::setUp();
        $this->factory = new Factory;
    }

    public function tearDown()
    {
        unset($this->factory);
    }

    /** @test **/
    public function it_is_initializable()
    {
        $this->assertInstanceOf('App\Src\Factory', $this->factory);
    }

    /** @test **/
    public function it_creates_file()
    {
        $real_path = '/home/vagrant/Code/DFParser\tests/raw/short/item_tool.txt';
        $real = $this->factory->newFile($real_path);
        $this->assertInstanceOf('App\Src\File',$real);

        $foul = $this->factory->newFile('wrong/path');
        $this->assertFalse($foul);

        $this->assertEquals($real, $this->factory->files[0]);
    }

    /** @test **/
    public function it_loads_files_from_token()
    {
        $factory = $this->factory;

        $token = 'notAToken';
        $files = $factory->loadFilesWithToken($token);
        $this->assertEmpty($files);

        $token = 'reaction';
        $files = $factory->loadFilesWithToken($token);
        $this->assertNotEmpty($files);
    }

}