<?php

namespace App\Src;

use App\Src\Factory;

/**
 * Class File stands for single txt file.
 * 
 * @package App\Src
 */
class File
{
	public $path = '';
    public $text = '';
    public $bObj = [];
    public $items = [];

    /**
     * @param string  $path
     */
	public function __construct ($path)
    {
        $this->path = $path;
        $this->factory = new Factory;
    }

    /**
     * Core function, performs every action required
     * to make object useful.
     *
     * @return $this
     */
    public function loadEverything ()
    {
        $this->loadText()
            ->masterworkRawFix()
            ->formArray()
            ->splitByObject();
        return $this;
    }

    /**
     * Load text from path
     *
     * @return $this
     */
    public function loadText () {
        $path = $this->path;
        $this->text = file_get_contents ($path);
        return $this;
    }

    /**
     * Object check and to array conversion
     * TODO Once again, regexp
     *
     * @return $this|bool
     */
    public function formArray()
    {
        $text = $this->text;
        $start = strpos($text, '[OBJECT:') + 8;
        $end = strpos($text, ']', $start + 1);
        if (!isset($start, $end))
            return sf::setTest(3, 'OBJECT is not defined in real');

        $string = $this->bObj['string'] = substr($text, $start, $end - $start);
        $this->text = str_replace('[OBJECT:'. $string. ']', "", $text);

        $file_map = \Config::get('constants.file_map');

        if (isset($file_map[$string]))
            $this->bObj['array'] = $file_map[$string];
        else
            $this->bObj['array'] = [$string];

        return $this;
    }

    /**
     * Extracts token from path string
     * Example: objects\item_tool_gnome.txt -> item_tool
     *
     * @return string|bool
     */
    public function getTokenFromPath()
    {
        $path = $this->path;
        $pattern = '/item_[a-z]+/';
        if(preg_match($pattern, $path, $match))
            return $match;
    }

    /**
     * Create multiple Item instances from text of single one.
     * If only_object is set, function won't split but only_object.
     *
     * @param  string [$only_object]
     *
     * @return string
     */
	public function splitByObject ($only_object = ''){

		if (!$this->bObj)
			return sf::setTest(3, '"bObj" property of File instance is missing: '.$this->bObj);

		$objects = $this->bObj['array'];
		$text = $this->text;
		$count = 0;
		$start_1 = $start_2 = false;
		$str_length = strlen($text);

		if($only_object && !strpos($text, '['. $only_object.':'))
			return false;

		while(true) {
			// break if end of string
			if ($start_2 === $str_length)
				break;

			// find start_1 positions of the DFobj block, done only once
			if (!$start_2){
				foreach ($objects as $i => $object){
					$strpos = strpos($text, "[{$object}:", 0);
					if ($strpos !== false){
						if ($start_1 === false)
							$start_1 = $strpos;
						else
							if ($start_1 > $strpos)
								$start_1 = $strpos;
					}
				}
				if ($start_1 === false)
					return false;
			}
			else $start_1 = $start_2; // otherwise, just attribute s1 to s2

			$start_2 = false;
			foreach ($objects as $i => $object){
				$strpos = strpos($text, "[{$object}:", $start_1+1);
				if ($strpos !== false)
					if ($start_2 === false)
						$start_2 = $strpos;
					else
						if ($start_2 > $strpos)
							$start_2 = $strpos;
			}
			if ($start_2 === false)
				$start_2 = $str_length;

			$end = strpos($text, ']', $start_1+1);
			$extr_obj = explode(':', substr($text, $start_1 + 1, $end - $start_1 - 1));

			// Extracts only specified object
			if ($only_object && $only_object !== $extr_obj[1])
				continue;

			// If only one object in file
			if ($start_2 === $str_length && $count === 0){
                $this->newItem($extr_obj, substr($text, $end+1, $start_2 - $start_1));
				break;
			}

			// while counter, just in case
			$count++;
			if ($count === 5000)
				return sf::setTest(3, $this->ID, __CLASS__, __METHOD__, 'More than 5000 objects in file.');

			// Create new item object
			$this->newItem($extr_obj, substr($text, $end+1, $start_2 - $start_1));
			sf::setTest(1, 'Loaded object :'.implode($extr_obj));
		}
		sf::setTest(1, 'Loaded '.$count.' objects.');
		//echo ('<br>Echo 3 (counter): '. count($this->items).'<br>');
		if (!count($this->items))
			return false;
		return $this;
	}

    /**
     * Fix corrupted masterwork raws ('!NOFOO!'->'YESFOO[')
     * TODO Could be improved by means of regexp
     *
     * @return $this
     */
    public function masterworkRawFix()
    {
        $mw = preg_match("/\\/MW\\//", $this->path, $matches);

        if ( ! $mw)
            return $this;

        $start = $end = 0;
        if(strpos($this->text,'!NO',$start) === FALSE)
            return $this;


        $words = array('corrupted' => array(), 'fixed' => array());

        while(true)
        {
            $start = strpos($this->text,'!NO',$start);
            $end = strpos($this->text, '!', $start+1);
            if ($start === FALSE or $end === FALSE or $end-$start > 30)
                break;
            $words['corrupted'][] = substr($this->text,$start,$end-$start+1);

            $start = $end;

        }

        foreach ($words['corrupted'] as $key=>$word)
            $words['fixed'][$key] = "YES".substr($word,3,-1).'[';

        $this->text = str_replace($words['corrupted'], $words['fixed'], $this->text);

        sf::setTest(1, 'MW real fixed');
        return $this;
    }

    /**
     * Create new Item and link it to file
     *
     * @param $sObj
     * @param $text
     *
     * @return Item
     */
    public function newItem($sObj, $text)
    {
        if (! (is_array($sObj) && is_string($text)))
            return false;
        $item = new Item($this, $sObj, $text);
        $this->items[] = $item;
        return ($item);
    }
}

