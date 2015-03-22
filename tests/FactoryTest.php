<?php

class FactoryTest extends TestCase {

    protected $factory;

    public function setUp()
    {
        parent::setUp();
        $this->factory = new App\Src\Factory;
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
    public function it_creates_object_of_given_class()
    {
        $className = 'Faker\Factory';
        $class = $this->factory->create($className);
        $this->assertInstanceOf($className, $class);

    }

    /** @test **/
    public function it_links_created_objects()
    {
        $className = 'Faker\Factory';
        $this->factory->create($className);
        $this->assertInstanceOf($className, $this->factory->link[0]);
    }

    /** @test **/
    public function it_validates_input()
    {
        $this->assertFalse($this->factory->create('NotAClassName'));
        $this->assertFalse($this->factory->create('Asva\DFParser\File',
            ['path'=>'InvalidPath']));
    }

    /** @test **/
    public function it_gets_file_from_string()
    {
        $factory = $this->factory;
        $path = '/home/vagrant/Code/dfparser/spec/test_raws/item_tool.txt';
        $file = $factory->getFile($path);
        $this->assertInstanceOf('Asva\DFParser\File', $file);
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