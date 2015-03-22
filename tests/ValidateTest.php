<?php

namespace Asva\DFParser;

class ValidateTest extends \TestCase {

    /** @test **/
    public function it_validates_path()
    {
        $valid_path = '/home/vagrant/Code/dfparser/spec/test_raws/!test1.txt';
        $invalid_path = 'path/to/nowhere.php';

        $valid = Validate::path($valid_path);
        $invalid = Validate::path($invalid_path);

        $this->assertTrue($valid);
        $this->assertFalse($invalid);
    }

    /** @test **/
    public function it_validates_class_name()
    {
        $valid_class_name = 'Asva\DFParser\File';
        $invalid_class_name = 'Non\Existent\Class';

        $valid = Validate::className($valid_class_name);
        $invalid = Validate::className($invalid_class_name);

        $this->assertTrue($valid);
        $this->assertFalse($invalid);
    }

    /**
     * Potentially dangerous shortcut.
     * Allows for shortened class names.
     * Default namespace is Asva\DFParser
     *
     * @test
     */
    public function it_validates_class_name_with_namespace()
    {
        $valid_class_name = 'File';
        $invalid_class_name = 'NotACLass';

        $valid = Validate::className($valid_class_name);
        $invalid = Validate::className($invalid_class_name);

        $this->assertTrue($valid);
        $this->assertFalse($invalid);
    }
}