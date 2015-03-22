<?php

namespace app\Src\DFParser;

class sf
{
    /**
     * Make readable text|html from array
     *
     * Example:
     * $data = [1,2,3];
     * $pattern = ['(',', ',')'];
     * sf::mergeWithPattern($data, $pattern)
     * // '(1, 2, 3)'
     *
     * @param array|string $data     Data to wrap
     * @param array|string $pattern  Wrapper pattern
     *
     * @return string
     */
    public static function mergeWithPattern($data = [], $pattern = [])
    {

        // Find depth
        // 0: empty, 1: string, 2: array, 3: 2-D array etc.
        $data_depth = sf::arrayDepth($data);
        $pattern_depth = sf::arrayDepth($pattern);

        //$v = print_r($pattern, true);

        //echo ('Data: '.$data_depth.'/'.$pattern_depth.'; "'.$v.'"";');

        switch ($data_depth)
        {
        case 0:  // empty: ''
        case 1:  // string
            switch ($pattern_depth) {
            case 0:
            case 1:
                return $data;
            case 2:
                if (count($pattern) > 1)
                    return $pattern[0].$data.end($pattern);
                else
                    return $data;
            default:
                foreach ($pattern as $pat)
                    $data = sf::mergeWithPattern($data, $pat);
                return $data;
            }
        case 2: // array
            switch ($pattern_depth) {
            case 0:
            case 1:
                if (count($data) > 1) {
                    $data = implode($data, $pattern);
                }
                return $data;
            case 2:
                $end = count($pattern) - 1;

                // If $pattern is one element array
                if ($end === 0) {
                    return (implode($data, $pattern[0]));
                }

                // Complex wrap if two or more elements
                $counter = 1;
                foreach ($data as $i => &$piece)
                {
                    // First element
                    if ($i === 0) {
                        $piece = $pattern[0] . $piece;
                    }

                    // Last element
                    if ($i === count($data) - 1) {
                        $piece = $piece . $pattern[$end];
                    } else {
                        // Skip middle if no middle
                        if ($end === 1) {
                            continue;
                        }

                        // Middle elements
                        $piece = $piece . $pattern[$counter];

                        // If there is several middle elements in the pattern
                        // we go through all af them,
                        // then we repeat next-to-last element
                        if ($counter < count($pattern) - 2) {
                            $counter++;
                        }
                    }
                }
                return implode($data);
            default:
                return sf::mergeWithPatternAndPop($data, $pattern);
            }
        case 3:
            switch ($pattern_depth)
            {
            case 0:
            case 1:
            case 2:
                foreach ($data as &$piece)
                    $piece = sf::mergeWithPattern($piece, $pattern);
                return implode($data);
            case 3:
                $first_pattern = array_pop($pattern);
                foreach ($data as &$piece)
                    $piece = sf::mergeWithPattern($piece, $pattern);
                $data = sf::mergeWithPattern($data, $first_pattern);
                return $data;
            default:
                $first_pattern = array_shift($pattern);

                if (count ($first_pattern) === 1)
                    foreach ($data as &$piece)
                        $piece = sf::mergeWithPattern($piece, $first_pattern);
                else
                    foreach ($data as $i => &$piece)
                    {
                        if ($i <= count ($first_pattern) - 1)
                            $piece = sf::mergeWithPattern($piece, $first_pattern[$i]);
                        else
                            $piece = sf::mergeWithPattern($piece, end($first_pattern));
                    }

                foreach ($pattern as $pat)
                    $data = sf::mergeWithPattern($data, $pat);
                return $data;
            }
        default:
            return sf::mergeWithPatternAndPop($data, $pattern);
        }
    }

    /**
     * Pops $pattern array, merges every $data element
     * with remaining $pattern. Then merges popped pattern
     * with $data and returns resulting string
     * Function partial:
     * @see sf::mergeWithPattern
     * @param array $data
     * @param array $pattern
     *
     * @return array|string
     */
    public static function mergeWithPatternAndPop(Array $data, Array $pattern)
    {
        $first_pattern = array_pop($pattern);
        foreach ($data as $i => &$piece)
            $piece = sf::mergeWithPattern($piece, $pattern);
        $data = sf::mergeWithPattern($data, $first_pattern);
        return $data;
    }

    /**
     * Explode string once for every delimiter
     *
     * @param $delimiters
     * @param $string
     *
     * @return array Multidimensional array
     */
    public static function multiexplode ($delimiters,$string) {
        $tmp = explode($delimiters[0],$string);
        array_shift($delimiters);
        if($delimiters != NULL)
            foreach($tmp as $key => $val)
                $tmp[$key] = sf::multiexplode($delimiters, $val);
        return  $tmp;
    }

    /**
     * Finds array depth, returns 0 if not array
     *
     * @param array|string $data
     * @return int
     */
    public static function arrayDepth(&$data = '')
    {
        if (!$data){
            $data = '';
            return 0;
        }

        if (! is_array($data))
            return 1;

        $max_indentation = 1;

        $array_str = print_r($data, true);
        $lines = explode("\n", $array_str);

        foreach ($lines as $line)
        {
            $indentation = (strlen($line) - strlen(ltrim($line))) / 4;
            if ($indentation > $max_indentation)
            {
                $max_indentation = $indentation;
            }
        }
        return ceil(($max_indentation - 1) / 2) + 2;
    }

    /**
     * Support function, error handling
     * TODO Add log processing (dispose of p. 2)
     *
     * @param int    $cleared
     * @param string $ID
     * @param string $more_text
     *
     * @return bool
     */
    public static function setTest($cleared = 1 /*1 - green, 2 - yellow, 3 - red*/, $ID = '?', $more_text = '.' /*strings*/){

        // 1) Backing up into global log
        $log_temp = array("cleared" => $cleared, "more_text" => $more_text);
        \Session::push('asva_dfparser_log', $log_temp);

        // 2) Output handling
        $log_output = '<br>'.$more_text;

        switch($cleared){
            case 1:
                $log_output = '<span style = "color:green">'.$log_output.'</span>';
                break;
            case 2:
                $log_output = '<span style = "color:blue">'.$log_output.'</span>';
                break;
            case 3:
                $log_output = '<span style = "color:brown">'.$log_output.'</span>';
                break;
        }
        //echo ($log_output);
        return false;

    }

    #### Lists every object in readable way
    public static function showLog(){
        global $log;
        $log_tmp = '';
        foreach ($log as $log_line){
            $log_output = ($log_line["ID"].'| '
            .$log_line["_class"].' / '.
            $log_line["_method"].': '
            .$log_line["more_text"]);

            switch($log_line["cleared"]){
                case 1:
                    $log_output = '<br><span style = "color:green">'.$log_output.'</span>';
                    break;
                case 2:
                    $log_output = '<br><span style = "color:blue">'.$log_output.'</span>';
                    break;
                case 3:
                    $log_output = '<br><span style = "color:brown">'.$log_output.'</span>';
                    break;
            }
            $log_tmp .= $log_output;
        }
        echo $log_tmp;

    }

    #### Outputs __toString for any created object
    public static function var_dump_mod ( $ID = -1 ){

        global $gID;
        if ($ID !== -1)
            return $gID[$ID];

        $tmp = '<br><span style = "color:blue">Var_dump_mod:</span><br>';
        foreach ($gID as $key => &$element)
            $tmp .= '<span style = "color:blue">'.$key.' | '.get_class($element).': </span>'.sf::toString($element).'<br>';
        echo ($tmp);
    }

    /**
     * Get class without namespace prefix
     *
     * @param  object $o
     *
     * @return string    Class name
     */
    public static function relativeClass ($o)
    {
        $class = get_class($o);

        $prefix = __NAMESPACE__."\\";
        $len = strlen($prefix);

        if (strncmp($prefix, $class, $len) === 0)
            return substr($class, $len);
        else
            return $class;
    }

    /**
     * Converts snake_case to camelCase
     *
     * @param string $val
     * @return string
     */
    public static function snakeToCamel($val) {
        $val = str_replace(' ', '', ucwords(str_replace('_', ' ', $val)));
        $val = strtolower(substr($val,0,1)).substr($val,1);
        return $val;
    }
}

?>