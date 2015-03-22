<?php

namespace app\Src\DFParser;

require "autoload.php";
require "vendor/autoload.php";
require "Src/constants.php";
#error display
ini_set('display_errors', 'On');
error_reporting(E_ALL);

# Global assignment
global $log; $log = array(); // Error log
global $gID; $gID = array(); // Unique ID
// Another global is $con, contains every constant,
// defined in constants.php

/**
 * Script holds 3 levels of abstraction:
 * 1) File - equals txt file;
 * 2) mRaw - equals object in File;
 * 3) various classes.
 *
 * # File scope classes (Scope 0)
 * include 'File.php';
 * include 'bText.php';
 * include 'bObj.php';
 *
 *     # DF-object size classes (Scope 1)
 *     include 'Item.php';
 *     include 'sText.php';
 *     include 'sObj.php';
 *
 *         # Branch classes (Scope 2)
 *         include 'path.php';
 *         include 'tag.php';
 *
 *         # tag inclusions
 *         include 't_objects.php';
 *
 * # constants
 * include 'constants.php';
 *
 * # Error handling and support functions
 * include 'error.php';
 */

# main function ATM
function tests(){

    global $gID;

    File::_new('!test3.txt');

    foreach ($gID as $key => $element)
        if (sf::relativeClass($element) === 'File'){
            $element->split_by_object();
            $element->sRaw[1]->tag->interpret_1();
        }

    sf::showLog();
    sf::var_dump_mod();
}

tests();

?>