<?php

namespace Asva\DFParser;


/**
 * Class Validate
 * Handy function shortcuts with injected logging
 *
 * @package Asva\DFParser
 */
class Validate{

    public static function path ($value){
        if ( ! file_exists ($value))
            return sf::setTest(3, 'Invalid path to file: "'.$value.'".');
        return true;
    }

    public static function className ($value){
        if ( ! class_exists ($value))
            return sf::setTest(3, 'Invalid class name: "'.$value.'".');
        return true;
    }
}