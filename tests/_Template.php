<?php

namespace Asva\DFParser;

class NAMETest extends \TestCase
{
    protected $NAME;

    public function setUp()
    {
        $this->NAME = new NAME;
    }

    public function tearDown()
    {
        unset($this->NAME);
    }

    /** @test **/
    public function it_names()
    {
        $this->assertTrue(1);
    }

}
