<?php

namespace App\Src;

use Illuminate\Support\Facades\Config;

class sfTest extends \TestCase
{
    /** @test **/
    public function test_arrayDepth()
    {
        $suit = [
            0 => [],
            1 => 'string',
            2 => [1,2,3],
            3 => [2 => [1,2,3]],
            4 => [2=> [3 => [1,2,3]]],
        ];

        foreach ($suit as $should => $data)
        {
            $is = sf::arrayDepth($data);
            $this->assertEquals($should, $is);
        }
    }

    /**
     * @test
     * @see Asva\DFParser\sf::arrayDepth();
     **/
    public function test_mergeWithPattern()
    {
        $suit = [
            // $data  | $pattern      | $should
            [ 1,       [],             1           ], // 1|0  That here
            [ [1,2,3], ':',            '1:2:3'     ], // 2|1  is depth
            [ 1,       [[':',':'],     []],':1:'   ], // 1|2  of $data
            [ [1,2,3], ['[',']'],      '[123]'     ], // 2|2  and $pattern
            [ [1,2,3], [['[',']'],[]], '[1][2][3]' ], // 2|3  accordingly*
            [
                [[1,2,3,4],[4,5,6,7],[7,8,9,9]],      // 3|3
                [['(s',')(s',')'], ['[',']'], ['t','t']],
                't[(s1)(s2)(s3)(s4)][(s4)(s5)(s6)(s7)][(s7)(s8)(s9)(s9)]t'
            ],
            [
                [[1,2,3],[4,5,6],[7,8,9]],            // 3|3
                [['[',':',']'],['(',')']],
                '([1:2:3][4:5:6][7:8:9])'
            ],
            [
                [[1,2,3],[4,5,6],[7,8,9]],            // 3|4
                [[['[','][',']'],['(',')(',')']],['r','rr','r'],['t','t']],
                'tr[1][2][3]rr(4)(5)(6)rr(7)(8)(9)rt'
            ],
        ];

        foreach ($suit as $case)
        {
            list($data, $pattern, $should) = $case;
            $is = sf::mergeWithPattern($data, $pattern);
            $this->assertEquals($should, $is);
        }
    }

}
