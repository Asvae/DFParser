<?php

namespace app\Src\DFParser;

/**
 * Item class which corresponds to single OBJECT
 */
class Item
{

    /**
     * @var File|string
     */
    public $file;

    /**
     * @var array
     */
    public $sObj;

    /**
     * @var string
     */
    public $text;

    /**
     * Constant
     * @var array
     */
    protected $filters;

    /**
     * @param File   $file       parent reference
     * @param array  $array_obj  type of object
     * @param string $text       file text
     */
	public function __construct (File $file, Array $array_obj, $text) {

		$this->file = $file;
		$this->sObj = $array_obj;
		$this->text = $text;

        $item_type = strtolower($file->bObj['string']);
        $this->filters = \Config::get('constants.filters_'.$item_type);
        if (!$this->filters)
            $this->filters = \Config::get('constants.filters_building');

        $this->ripTag();
	}

    /**
     * Performs various manipulations with tags
     * making them readable as html.
     *
     * @return bool
     */
    public function process()
    {
        $filters = $this->filters;
        foreach ($filters as $key => $filter) {
            $param = &$this->$key;

            if (! isset($param['array'],$param['string']) || isset($param['html']))
                continue;
            $method = sf::snakeToCamel('html_' . $key);

            // Apply method if it's defined
            if (method_exists($this, $method)) {
                $this->$method();
            }
        }
        return true;
    }


    /**
     * Rip tags from text according to filter
     * As a result, for every tag we can now:
     * $this->some_tag['string']
     * $this->some_tag['array']
     *
     * @return $this
     */
    protected function ripTag()
    {
        $filters = $this->filters;

        // Rip tags from text according to filter
        foreach ($filters as $key => $filter)
        {
            if ( ! preg_match_all ($filter , $this->text, $matches))
                continue;

            // Find matches
            $this->$key = [];
            $tag = &$this->$key;
            $tag['string'] = $matches[0];

            // Store them
            foreach ($matches[0] as $match)
                $this->text = str_replace($match, '', $this->text);

            // Delete matches from source
            $this->text = preg_replace('/[\s\n]+/', '', $this->text);

            // Convert matches to arrays
            $this->generateArrays($key);
        }
        return $this;
    }

    /**
     * Generates array for $key tag. Meaning we can access it like:
     * $this->some_tag['array']
     * Helper function, used in ripTag
     *
     * @param string $key
     * @return bool
     */
    protected function generateArrays ($key = '')
    {
        $tag = &$this->$key;

        if (! isset($tag['string']))
            return sf::setTest(2, 'Tag string is not defined');

        if (isset($tag['array']))
            return sf::setTest(2, 'Tag array is already set');

        foreach ($tag['string'] as $text)
        {
            $tag_tmp = [];
            $off = 0;

            while (1)
            {
                $start = strpos($text, '[', $off);
                if ($start === FALSE)
                    break;

                $end = strpos($text, ']', $start);
                if ($end === FALSE)
                    break;

                $group = substr($text, $start + 1, $end - $start - 1);
                $tag_tmp[] = explode(':', $group);
                $off = $end + 1;
            }

            $tag['array'][] = $tag_tmp;
        }
        sf::setTest(1, 'Arrays were generated.');

        return true;
    }

//	$item = $this->build_item[$itemID];
//
//	echo ('Echo 1 (search for item): [' .implode($item,':'). ']<br>');
//
//	switch ($item[1]){
//		case 'TOOL':
//			$this->factory->create('item_tool.txt');
//            $this->factory->create('item_tool_masterwork.txt');
//			break;
//	}

/*************************************************************
 * These are conversion methods. They are employed by $this->process()
 * The naming convention is word  'html'+parameter name in camelCase
 * For example: htmlBuildItem() stands for build_item.
 *************************************************************/


    /**
     * Makes workshop image in ASCII-table format
     *
     * @param int $build_stage
     * @return $this
     */
    protected function htmlColor($build_stage = 3){

        // Conversion tables
        $foreground = \Config::get('constants.conv_color_foregr');
        $background = \Config::get('constants.conv_color_backgr');
        $unicode =    \Config::get('constants.conv_unicode');

        // TODO Move this check to somewhere else
        // Check for required tags
        if (! $this->tile)
            return sf::setTest (2, 'Tile is missing.');

        // Shorting references
        $tile_array = $this->tile['array'];
        $color_array = $this->color['array'];

        $output = [];
        foreach ($color_array as $color_subarray)
        {
            foreach ($color_subarray as $i => $row)
            {
                if (+$row[1] !== $build_stage)
                    continue;

                for ($j = 0; $j <= (count($row)-4) / 3; $j++)
                {
                    $tmp = implode(":", array_slice($row, 3 + 3*$j, 3));
                    $output[$i][$j][0] = $foreground[$tmp];
                    $output[$i][$j][1] = $background[$tmp];
                }
            }
        }

        foreach ($tile_array as $tile_subarray)
        {
            $i = 0;
            foreach ($tile_subarray as $row)
            {
                if (+$row[1] !== $build_stage)
                    continue;

                foreach (range(3,9) as $j => $key)
                {
                    $output[$i][$j][2] = $unicode[$row[$key]];
                }
                $i++;
            }
        }

        $this->htmlize ($output, 'color');
        return $this;
    }

    /**
     * Makes workshop block in ASCII-table format
     *
     * @return bool|string
     */
    protected function htmlBlock(){

        // Check for required tags
        if (!isset($this->work_location))
            return sf::setTest (3, 'work_location is missing');

        $block_array = $this->block['array'][0];
        $work_location_array = $this->work_location['array'][0][0];

        $array_tmp = [];
        foreach ($block_array as $i=>&$blocks) {
            // Inelegant, but disposes of 2 first elements
            array_shift($blocks);
            array_shift($blocks);

            foreach ($blocks as $j=>$block) {

                // work location tile
                if ($i == $work_location_array[0]-1
                    &&  $j == $work_location_array[1]-1){
                    $array_tmp[$i][$j] = '#000080';
                    continue;
                }
                // Blocked tile
                if (!$block)
                    $array_tmp[$i][$j] = '#00FF00';
                else
                    // And free tile
                    $array_tmp[$i][$j] = '#008000';
            }
        }

        $this->htmlize ($array_tmp, 'block');
        return $this;
    }

    /**
     * Generate html for build_key
     * Makes "Alt+Ctrl+S" from "CUSTOM_SHIFT_ALT_CTRL_S"
     *
     * @return $this
     */
    protected function htmlBuildKey ()
    {
        $key = $this->build_key['array'][0][0][1];
        $key = explode("_", $key);
        $key_array = [];

        // Push modifier keys to array
        foreach ($key as &$partial)
        {
            switch ($partial){
            case 'SHIFT':
            case 'ALT':
            case 'CTRL':
                $key_array[] = ucfirst(strtolower($partial));
            }
        }

        // Push actual key to array
        $key_array[] = strtolower(end($key));

        $this->htmlize ($key_array, 'build_key');
        return $this;
    }

    /**
     * Generate html for build_labor
     *
     * @return $this
     */
    protected function htmlBuildLabor ()
    {
        $build_labor = $this->build_labor['array'][0][0][1];

        $this->htmlize ($build_labor, 'build_labor');
        return $this;
    }

    /**
     * Generate html for dim
     *
     * @return $this
     */
    protected function htmlDim ()
    {
        $dim = $this->dim['array'][0][0];
        array_shift($dim);

        $this->htmlize ($dim, 'dim');
        return $this;
    }

    /**
     * Generate html for name
     *
     * @return $this
     */
    protected function htmlName ()
    {
        $name = $this->name['array'][0][0][1];

        $this->htmlize ($name, 'name');
        return $this;
    }

    /**
     * Generate html for name_color
     *
     * @return $this
     */
    protected function htmlNameColor ()
    {
        $name_color = $this->name_color['array'][0][0];
        array_shift($name_color);
        $name_color = implode($name_color,':');

        $conv_color_foregr = \Config::get('constants.conv_color_foregr');

        $this->htmlize ($conv_color_foregr[$name_color], 'name_color');
        return $this;
    }

    /**
     * Generate html for build key
     *
     * @return $this
     */
    protected function htmlBuildItem ()
    {
        // Constant, contains simple item names
        $simple_item = \Config::get('constants.simple_item');

        $build_item = $this->build_item['array'][0];

        $build_item_tmp = [];
        foreach ($build_item as $i => &$item) {

            // Get item quantity
            $build_item_tmp[$i][] = $item[1];

            // If item is defined by one word take it from constant
            if (in_array($item[2], array_keys($simple_item), true))
                $build_item_tmp[$i][] = $simple_item[$item[2]];
            elseif( in_array($item[2], array_values($simple_item), true))
                $build_item_tmp[$i][] = ucfirst(strtolower($item[2]));

            // Otherwise lookup in files
            else
            {
                $token = 'item_' . strtolower($item[2]);
                $files = $this->factory->loadFilesWithToken($token);
                foreach($files as $key => $file)
                    foreach($file->item as $single_item)
                        if ($single_item->sObj[1] === $item[3]
                            && isset ($single_item->item_name))
                            $build_item_tmp[$i][] =
                                ucfirst($single_item->item_name['array'][0][0][1]);

            }

            // Provide information in case search still failed
            if ( ! isset($build_item_tmp[$i][1])) {
                $build_item_tmp[$i][] = 'Unknown item: ' . $item[2].':'.$item[3];
                sf::setTest(3, 'Unknown item: ' . $item[2].':'.$item[3]);
            }
        }

        $this->htmlize ($build_item_tmp, 'build_item');
        return $this;
    }

    /**
     * Given array and pattern type extracts pattern from global,
     * then applies it to array.
     * Array is saved in {$this->$type}['html']
     *
     * @param array  $data
     * @param string $type
     *
     * @return bool
     */
    protected function htmlize ($data = [], $type = '')
    {
        // Do nothing:
        // If prerequisites are missing
        if (! isset ($this->$type, \Config::get('constants.patterns')[$type]))
            return false;

        // And if html is already defined
        $element = &$this->$type;
        if (isset ($element['html']))
            return true;

        // Otherwise generate html from passed $data
        $pattern = \Config::get('constants.patterns')[$type];
        $element['html'] =sf::mergeWithPattern($data, $pattern);

        return true;
    }


}

?>