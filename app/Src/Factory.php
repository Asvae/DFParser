<?php

namespace App\Src;

class Factory{

    /**
     * Array of created Files
     * @var array
     */
    public $files;

    /**
     * Constructor
     */
    public function __construct() {
        $this->files = [];
    }

    /**
     * Get File object with name or create if doesn't exist.
     *
     * @param string $path
     * @return bool
     */
    public function getFile($path = '')
    {
        if( ! (is_string($path) && $path))
            return false;

        // Check if file is already loaded
        // and return if it's clearly so.
        foreach ($this->files as $file){
            if ($file->path === $path)
                return $file;
        }

        // Otherwise create new object.
        return $this->newFile($path);
    }

    /**
     * Make file from string
     *
     * @param string $path
     *
     * @return File|bool
     */
    public function newFile($path = ''){

        // Return false if wrong path
        if ( ! $this->validatePath ($path))
            return sf::setTest(3, 'Invalid path to file: "'.$path.'".');

        $file = new File($path, $this);
        $this->files[] = $file;
        return $file;
    }

    /**
     * validate path
     *
     * @param string $path
     *
     * @return bool
     */
    public static function validatePath ($path = ''){
        if ( ! file_exists ($path))
            return false;
        return true;
    }

    /**
     * Load files first part of which corresponds with token
     * TODO move default folder to some other file
     *
     * @param string $token
     *
     * @return array
     */
    public function loadFilesWithToken($token = ''){
        if ( ! $token)
            return [];

        $files = glob('/home/vagrant/Code/DFParser/tests/raw/real/'.$token.'*');
        if (! $files)
            return [];

        $file_references = [];
        foreach($files as $path){
            $file_references[$path] = $this->newFile($path);
            $file_references[$path]->loadEverything();
        }

        return $file_references;
    }
}