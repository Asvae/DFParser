<?php

namespace App\Src;

class Factory{

    /**
     * Array containing references to created objects
     * @var array
     */
    public $link;

    /**
     * Constructor
     */
    public function __construct() {
        $this->link = [];
    }

    public function loadFilesWithToken($token = ''){
        if ( ! $token)
            return [];

        $files = glob('/home/vagrant/Code/dfparser/tests/real/'.$token.'*');
        if (! $files)
            return [];

        $file_references = [];
        foreach($files as $path){
            $file_references[$path] = $this->getFile($path);
            $file_references[$path]->loadEverything();
        }

        return $file_references;
    }

    /**
     * Get File object with name
     *
     * @param string $path
     * @return bool
     */
    public function getFile($path = '')
    {
        if( ! (is_string($path) && $path))
            return false;

        // Check if such an object is already present.
        // Return object reference if so.
        foreach ($this->link as $link){
            if (isset($link->path) && $link->path === $path)
                return $link;
        }

        // Otherwise create new object
        return $this->create('Asva\DFParser\File', ['path' => $path]);
    }

    /**
     * Links object to factory so that you can access
     * object from factory and vice versa
     *
     * @param $fruit
     *
     * @return mixed
     */
    protected function linkObject($fruit){
        $this->link[] = $fruit;
        $fruit->factory = $this;
        return $fruit;
    }

    /**
     * Pass an instance from object name string
     *
     * @param string $ClassName
     * @param array  $params
     *
     * @return object $ClassName
     */
    public function create($ClassName, $params=[])
    {
        // Validation
        if( ! $this->validate($ClassName, $params)) return false;

        // Create object
        $object = $this->makeObject($ClassName, $params);

        // Create a reference link from factory.
        // Return created object
        return $this->linkObject($object);
    }

    /**
     * Instantiate passed class with params
     *
     * @param string $ClassName
     * @param array  $params
     * @return object
     */
    protected function makeObject ($ClassName, $params)
    {
        return (new \ReflectionClass($ClassName))
            ->newInstanceArgs($params);
    }

    /**
     * Validator helper function.
     * You get true if everything's fine. False otherwise.
     * Check the session log for wrongs.
     *
     * @param $ClassName    MUST be valid
     * @param $params       if validator is defined MUST be valid
     * @return bool
     */
    protected function validate(&$ClassName, $params)
    {
        // Trying to fix ClassName if it's wrong
        if( ! (Validate::className($ClassName))) {
            $ClassName_prefixed = __NAMESPACE__ . '\\' . $ClassName;

            // Wrong ClassName is no no, naturally
            if (!(Validate::className($ClassName_prefixed))) {
                return false;
            } else {
                $ClassName = $ClassName_prefixed;
            }
        }

        // Now for params
        if ($params) foreach ($params as $key=>$param) {

            // 1. Return validation result if there is an according method
            if( method_exists ( 'Asva\DFParser\Validate', $key ))
                return Validate::$key($param);

            // 2. Pass and log everything else
            sf::setTest(2,
                'Parameter without validation passed'.
                ' for class creation: "Key: '.$key.
                ' Value:'.'".');

        }

        return true;
    }
}