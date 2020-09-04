<style>
    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
        text-align: center;
    }


    table {
        border-top: 2px solid #000;
        border-left: 2px solid #000;
        /* position: fixed; */
        top: 20;
        right: 20;
    }

    td {
        padding: 5px 0px;
        min-width: 10px;
    }

    table tr:nth-of-type(3n) td {
        border-bottom: 2px solid #000;
    }

    td:nth-child(3n+0) {
        border-right: 2px solid #000;

    }

    td.highlight {
        color: #bf4306;
        font-weight: bold;
    }
</style>
<?php

class Sudoku
{
    private $_unsolved_sudoku;
    private $_transposed;
    private $_blocks;
    private $_blocks_to_traverse;
    private $_probable_sudoku;
    // private $_probable_sudoku;
    private $_chances;

    private $_repeater_found = 0;


    private $_block_WO_biggest_block;
    private $_trim_blocks;
    private $_block_to_solve;
    private $_k1, $_k2;

    public function __construct($unsolved_sudoku)
    {
        $this->_unsolved_sudoku = $unsolved_sudoku;
        $this->set_blocks();
        $this->transpose_blocks();
    }

    /**
     * Solve Suduko
     *
     * @return void
     */
    public function solve()
    {
        $this->transpose_blocks();
        $this->set_blocks();
        // $this->print_sudoku();
        $this->_probable_sudoku = $this->_unsolved_sudoku;
        // $this->solve2();

        $possible = [];
        // print_r($this->_blocks);
        // print_r($this->_blocks_to_traverse);

        foreach ($this->_unsolved_sudoku as $k => $v) {

            foreach ($v as $kk => $vv) {
                // echo '<br>' . $k . ',' . $kk . '<br>';

                if ($vv === 0) {
                    // echo '<br>-------------------------Empty cell<br>';
                    for ($i = 1; $i <= 9; $i++) {
                        // echo '<br>----------------------------------------------------------------------------------------------------Testing for: ' . $i;
                        $valid = true;


                        $block = 1;
                        if ($kk > 2) $block = 2;
                        if ($kk > 5) $block = 3;

                        $block_row = 1;
                        if ($k > 2) $block_row = 2;
                        if ($k > 5) $block_row = 3;
                        // echo '---------------check block:' . (in_array($i, $this->_blocks[$block_row][$block]) ? 'fail' : 'pass') . '<br>';


                        if ($valid && in_array($i, $this->_blocks[$block_row][$block])) {
                            // echo '<br>======= Inside 3rd check on: ' . $i . '<br>';
                            // echo json_encode($this->_blocks[$block_row][$block]);

                            // print_r($this->_blocks[$block_row][$block]);
                            $valid = false;
                        }

                        // echo '<br>---------------check rows:' . (in_array($i, $this->_unsolved_sudoku[$k]) ? 'fail' : 'pass') . '<br>';

                        if (in_array($i, $this->_unsolved_sudoku[$k])) {
                            // echo '<br>======= Inside 1st check on: ' . $i . '<br>';
                            // echo json_encode($this->_unsolved_sudoku[$k]);
                            // print_r($this->_unsolved_sudoku[$k]);
                            $valid = false;
                        }

                        // echo '---------------check columns:' . (in_array($i, $this->_transposed[$kk]) ? 'fail' : 'pass') . '<br>';

                        if ($valid && in_array($i, $this->_transposed[$kk])) {
                            // echo '<br>======= Inside 2nd check on: ' . $i . '<br>';
                            // echo json_encode($this->_transposed[$kk]);

                            // print_r($this->_transposed[$k]);
                            $valid = false;
                        }


                        if ($valid) {

                            $this->_probable_sudoku[$k][$kk] = '0' . $i;
                            // $this->_unsolved_sudoku[$k][$kk] = '0' . $i;
                            $this->transpose_blocks($this->_probable_sudoku);
                            $this->set_blocks($this->_probable_sudoku);
                            // $possible[]=$this->_probable_sudoku;
                            // foreach($possible as $p => $pv){
                            // echo '<br>';
                            // $this->print_sudoku($pv);
                            // $this->print_sudoku($this->_probable_sudoku);
                            // $this->print_sudoku();
                            // }
                            break;
                        }
                        // if ($k > 1) {
                        //     break 3;
                        // }
                    }
                }
            }
        }

        //get the biggest block first
        // $bb = $this->get_biggest_block();

        // print_r($this->_blocks[$this->_k1][$this->_k2]);

        // foreach ($this->_unsolved_sudoku as $k => $v) {
        // }

        //print sudoku
        // $this->print_sudoku();


        // $this->print_sudoku($this->_transposed);
        // $this->solve2();
    }


    public function solve2($unsolved_sudoku = null)
    {
        $unsolved_sudoku = $unsolved_sudoku ?: $this->_unsolved_sudoku;
        $this->set_blocks($unsolved_sudoku);
        $this->transpose_blocks($unsolved_sudoku);
        $this->_probable_sudoku = $unsolved_sudoku;

        $this->_chances = [];
        for ($i = 1; $i <= 9; $i++) {
            foreach ($this->_blocks_to_traverse as $blocks_row => $bs) { //3 blocks per loop
                foreach ($bs as $block => $b) { //1 block per loop
                    $block_col = 0;
                    $block_row = 0; //traversing on block rows 3 on top then start from 0 next line then next line.
                    $blk = 1; //row of block
                    $skip = 0;
                    $success = 1;
                    $bb = $this->_blocks[$blocks_row][$block];

                    if (!in_array($i, $bb)) {
                        foreach ($b as $ttt => $c) { //1 cell of block per loop


                            if ($ttt > 2 && $blk == 1) {
                                $blk = 2;
                                $skip = 0;
                                $block_row++;
                                $block_col = 0;
                            }
                            if ($ttt > 5 && $blk == 2) {
                                $blk = 3;
                                $skip = 0;
                                $block_row++;
                                $block_col = 0;
                            }

                            if ($c == 0 || strlen($c) > 1) {

                                if ($skip) continue;

                                //check repeater on row
                                $search_row = (($blocks_row * 3) - 3) + $block_row;
                                if (in_array($i, $this->_unsolved_sudoku[$search_row])) {
                                    $skip = 1;
                                    $success = 0;
                                }

                                //check repeater on column
                                $search_col = (($block * 3) - 3) + $block_col;
                                if (!$skip && in_array($i, $this->_transposed[$search_col])) $success = 0;


                                //chances for placement e.g. chances for 1 in a single cell of block
                                if ($success) {
                                    $this->_chances[$i][$blocks_row][$block][$block_row][$block_col] = 1;
                                }

                                $success  = 1;
                            }

                            if ($block_col < 2) $block_col++;
                        }
                    }
                }
            }
        }

        $this->place_num();
    }

    private function place_num()
    {
        foreach ($this->_chances as $nk => $ns) { // numbers
            foreach ($ns as $bsk => $blks) { // blocks
                // print_r($blks);
                // echo "<br>---------------------------------------------------------------------<br>";
                foreach ($blks as $bk => $blk) { // block
                    foreach ($blk as $brk => $blkrow) { // block row
                        foreach ($blkrow as $bck => $blkcol) { // block cell
                            // echo '<br>numbers: ' . $nk . '<br>blocks: ' . $bsk . '<br>block: ' . $bk;
                            // print_r($blk);
                            $col = (($bk * 3) - 3) + $bck;
                            $row = (($bsk * 3) - 3) + $brk;

                            if ($this->check_value_placement($row, $col,  $nk, $bsk, $bk, $brk, $bck)) {
                                $this->_probable_sudoku[$row][$col] = '0' . round($nk);
                                $this->set_blocks($this->_probable_sudoku);
                            }

                            // $this->_probable_sudoku[$row][$col] = '0' . $nk;
                        }
                        // echo '<br>row: '.$row.',col:'. $col.', nk:'.  $nk.',bsk:'. $bsk.',bk:'. $bk.',brk:'. $brk.',bck:'. $bck.'<br>';
                    }
                }
            }
            // $this->finalize($nk); //after 1 number is placed in all blocks as suitable by chances table.
        }
        // $this->set_blocks($this->_probable_sudoku);
        // print_r($this->_blocks_to_traverse);

        // $this->remove_repeater();
        $this->print_sudoku($this->_probable_sudoku);
        if ($this->_repeater_found == 0) {
        } else {
            $this->_repeater_found++;
            $this->solve2($this->_probable_sudoku);
        }
    }

    /**
     * check if the value is suitable for location in sudoku cell
     *
     * @param [int] $row
     * @param [int] $col
     * @param [int] $val
     * @return boolean
     */
    private function check_value_placement($row, $col, $new_val, $blocks, $block, $block_row, $block_cell)
    {
        // $ret  = false; //return decision for the new value to be placed in the cell or not.
        $this->set_blocks($this->_probable_sudoku); //set 3x3 blocks
        $this->transpose_blocks($this->_probable_sudoku); //transpose latest changes for column repetition test


        // print_r($this->_blocks_to_traverse);
        // print_r($this->_chances);
        // echo '<br> row: ' . $row . ', col: ' . $col . ', val: ' . $new_val;

        $u_blocks = $this->_blocks_to_traverse[$blocks][$block];
        $old_val = $this->_probable_sudoku[$row][$col];
        $r_old_val = round($old_val);
        //if the number is 1 place the value for now
        // if ($new_val == 1) {
        //     return true;
        // }

        //if cell is empty place the value for now
        if ($old_val == 0) {
            return true;
        }

        //if cell is not empty check for following conditions

        //compare current cell value and value to change chance

        //get current cell value
        // $old_val_chances = isset($this->_chances[$old_val][$blocks][$block][$block_row][$block_cell]) ?
        //     $this->_chances[$old_val][$blocks][$block][$block_row][$block_cell] : [];
        // $new_val_chances = isset($this->_chances[$new_val][$blocks][$block][$block_row][$block_cell]) ?
        //     $this->_chances[$new_val][$blocks][$block][$block_row][$block_cell] : [];

        $old_val_chances = isset($this->_chances[$r_old_val][$blocks][$block]) ? $this->_chances[$r_old_val][$blocks][$block] : [];

        $new_val_chances = isset($this->_chances[$new_val][$blocks][$block]) ? $this->_chances[$new_val][$blocks][$block] : [];

        // echo '<br> Old Number:' . $old_val . ' | blocks:' . $blocks . ' | block:' . $block;
        // echo '<br> current value: ' . $old_val;
        // echo '<br> current value chances: ';
        // print_r(count($old_val_chances, COUNT_RECURSIVE));
        // print_r($old_val_chances);


        // echo '<br><br><br><br> New Number:' . $new_val . ' | blocks:' . $blocks . ' | block:' . $block;
        // echo '<br> my value: ' . $new_val;
        // echo '<br> my value chances: ';
        // print_r(count($new_val_chances, COUNT_RECURSIVE));
        // print_r($new_val_chances);


        // $who_is_more = array_count_values($this->_blocks_to_traverse[$blocks][$block]);
        $who_is_more = array_count_values($u_blocks);
        // print_r($u_blocks);


        // if (
        //     isset($who_is_more[$this->_probable_sudoku[$row][$col]]) &&
        //     isset($who_is_more[$new_val]) &&
        //     $who_is_more[$this->_probable_sudoku[$row][$col]] > isset($who_is_more[$new_val])
        // )

        //is old value chance is greater than new value
        // echo '<br><br><br>Blocks:'.$blocks.',Block:'.$block.'-----------------<br>old:' . $old_val . ', new:' . $new_val . '<br>';
        // print_r($who_is_more[$old_val]);
        // echo '<br>who is more:<br>';
        // print_r($who_is_more);

        if (count($old_val_chances, COUNT_RECURSIVE) > count($new_val_chances, COUNT_RECURSIVE)) {
            // echo '<br>current:' . $this->_probable_sudoku[$row][$col];
            // echo '<br>My value:' . $new_val . '<br>';
            // print_r($who_is_more[$this->_probable_sudoku[$row][$col]]);
            // $instances = array_count_values($this->_probable_sudoku[$col])[$new_val];

            //is old value repeating more than once in the BLOCK
        }
        if (isset($who_is_more[$old_val]) && $who_is_more[$old_val] > 1) {
            // echo 'returned:' . $new_val;
            return true;
        }


        // if ($blocks == 2 && $block == 1) {
        //     // die();
        // }
        // if ($val == 3) {
        //     die();
        // }
        // $this->_probable_sudoku;

        return false;
    }


    private function remove_repeater($unsolved_sudoku = null)
    {
        $unsolved_sudoku = $unsolved_sudoku ?: $this->_unsolved_sudoku;

        // $this->_unsolved_sudoku = $unsolved_sudoku;
        $this->set_blocks($unsolved_sudoku);
        // $this->set_blocks($this->_probable_sudoku);
        $this->transpose_blocks($unsolved_sudoku);
        // $this->_probable_sudoku = $this->_probable_sudoku;
        $this->_probable_sudoku = $unsolved_sudoku;
        // $this->print_sudoku();
        echo '<br>--------------------------------------------repeater: ' . $this->_repeater_found . '<br>';
        // print_r($this->_blocks_to_traverse);

        $this->_chances = [];
        for ($i = 1; $i <= 9; $i++) {
            foreach ($this->_blocks_to_traverse as $blocks_row => $bs) { //3 blocks per loop
                foreach ($bs as $block => $b) { //1 block per loop
                    $block_col = 0;
                    $block_row = 0; //traversing on block rows 3 on top then start from 0 next line then next line.
                    $blk = 1; //row of block
                    $skip = 0;
                    $success = 1;
                    $bb = $this->_blocks[$blocks_row][$block];

                    // print_r($bb);

                    if (!in_array($i, $bb)) {
                        // $search_col = 0;
                        foreach ($b as $ttt => $c) { //1 cell of block per loop

                            // echo '<br>Cell value:' . $c . '<br>';
                            if ($ttt > 2 && $blk == 1) {
                                $blk = 2;
                                $skip = 0;
                                $block_row++;
                                $block_col = 0;
                                // echo '<br>block col: ' . $block_col . '<br>';
                            }
                            if ($ttt > 5 && $blk == 2) {
                                $blk = 3;
                                $skip = 0;
                                $block_row++;
                                $block_col = 0;
                                // echo '<br>block col: ' . $block_col . '<br>';
                            }
                            // echo '<br>C1:'.$c;

                            if ($c == 0 || strlen($c) > 1) {
                                // echo ',C2:'.$c.'<br>';

                                // echo '<br>Skip:' . $skip . '<br>';

                                if ($skip) continue;

                                // if (in_array($i, $this->_blocks[$block_row][$block])) {
                                //     // echo '<br>======= Inside 3rd check on: ' . $i . '<br>';
                                //     // echo json_encode($this->_blocks[$block_row][$block]);

                                //     // print_r($this->_blocks[$block_row][$block]);
                                //     $success = 0;
                                // }

                                $search_row = (($blocks_row * 3) - 3) + $block_row;

                                // echo '<br><br>Blocks:' . $blocks_row . ',Block:' . $block . ',Row:' . $block_row . ',cell:' . $ttt .
                                //     '<br> >>>---------------check rows:' . (in_array($i, $this->_unsolved_sudoku[$search_row]) ? 'fail' : 'pass');
                                // echo '<br>Row Values: ' . json_encode($this->_unsolved_sudoku[$search_row]) . '<br>';



                                if (in_array($i, $this->_unsolved_sudoku[$search_row])) {
                                    // echo '<br>======= Inside 1st check on: ' . $i . '<br>';
                                    // echo json_encode($this->_unsolved_sudoku[$search_row]);
                                    $skip = 1;
                                    $success = 0;
                                }

                                //$block_col = ($block*3)-1;

                                // if ($blk == 2 && $search_col < 3 && $search_col > 6) $search_col = 3;
                                // if ($blk == 3 && $search_col < 6) $search_col = 6;
                                $search_col = (($block * 3) - 3) + $block_col;

                                echo '<br><br>|||||||||Blocks:' . $blocks_row . ',Block:' . $block . ',Col:' . ($block_col) . ',cell:' . ($ttt)  .
                                    '<br> ]]]]---------------check columns:' . (in_array($i, $this->_transposed[$search_col]) ? 'fail' : 'pass');
                                echo '<br>Search col: ' . $search_col . ' <br>Column Values: ' . json_encode($this->_transposed[$search_col]) . '<br>';

                                if (!$skip && in_array($i, $this->_transposed[$search_col])) {
                                    //      echo '<br><br>|||||||||Blocks:' . $blocks_row . ',Block:' . $block . ',Col:' . ($block_col) . ',cell:' . ($ttt)  .
                                    //     '<br> ]]]]---------------check columns:' . (in_array($i, $this->_transposed[$search_col]) ? 'fail' : 'pass');
                                    // echo '<br>Search col: ' . $search_col . ' <br>Column Values: ' . json_encode($this->_transposed[$search_col]) . '<br>';

                                    echo '<br>Failed======= Inside 2nd check on: ' . $i . '<br>';
                                    // echo json_encode($this->_transposed[$search_col]);

                                    $success = 0;
                                }

                                //chances for numbers e.g. chances for 1 in a single cell of block
                                if ($success) {
                                    $this->_chances[$i][$blocks_row][$block][$block_row][$block_col] = 1;

                                    // $col = (($block * 3) - 3) + $block_col;
                                    // $row = (($blocks_row * 3) - 3) + $block_row;

                                    // if ($this->check_value_placement($row, $col,  $i, $blocks_row, $block, $block_row, $block_col)) {
                                    //     $this->_probable_sudoku[$row][$col] = '0' . $i;
                                    // }
                                }
                                $success  = 1;
                            }
                            // echo '<br>------------------------------------------------------------' . $block_col;
                            if ($block_col < 2) $block_col++;
                            // if ($ttt > 6 && $blk == 3) $block_col += 6;
                        }
                        // $block_col -= 3;
                    }
                }
            }
        }
        // $this->finalize();
        $this->place_num();

        // echo '<br> [number: [ blocks: [ block: [block row:[ cell key: chance, cell key: chance,... ],... ],...],... ],... ]<br>';
        // echo '<br>--------------------------------------- [number: [ blocks: [ block: [block row:[ cell key: chance, cell key: chance,... ],... ],...],... ],... ]<br>';
        // print_r($this->_chances);
        // $this->print_sudoku($this->_probable_sudoku);
    }

    /**
     * convert to small blocks of 3x3
     *
     * @return void
     */
    private function set_blocks($sudoku = null)
    {


        $original = $sudoku ? true : false;
        $sudoku = $sudoku ?: $this->_unsolved_sudoku;
        if ($original) {
            $this->_blocks_to_traverse = [];
        } else {
            $this->_blocks = [];
        }

        //get biggest block
        // $this->_block_WO_biggest_block = $this->_trim_blocks;

        foreach ($sudoku  as $key => $columns) {
            $row = 1;
            if ($row != 2 && $key > 2) $row = 2;
            if ($row != 3 && $key > 5) $row = 3;

            $col = 1;
            foreach ($columns as $col_key => $cols) {
                if ($col != 2 && $col_key > 2) $col = 2;
                if ($col != 3 && $col_key > 5) $col = 3;
                if ($original) {
                    $this->_blocks_to_traverse[$row][$col][] = $cols;
                } else {
                    $this->_blocks[$row][$col][] = $cols;
                }
            }
        }
        // print_r($this->_blocks);
        // print_r($this->_blocks);
        //get trimmed blocks
        // foreach ($this->_blocks as $k => $v) {
        //     foreach ($v as $kk => $vv) {
        //         $this->_trim_blocks[$k][$kk] = array_filter($vv);
        //     }
        // }
    }

    private function transpose_blocks($sudoku = null)
    {
        $this->_transposed = [];
        $sudoku = $sudoku ?: $this->_unsolved_sudoku;
        foreach ($sudoku as $k => $v) {
            foreach ($v as $kk => $vv) {
                $this->_transposed[$kk][] = $vv;
            }
        }
    }

    /**
     * Print sudoku blocks on screen
     *
     * @return void
     */
    private function print_sudoku($sudoku = null)
    {
        $sudoku = $sudoku ?: $this->_unsolved_sudoku;

        echo '<table style="min-width:250px">';
        foreach ($sudoku as $k => $v) {
            echo "<tr>";
            foreach ($v as $kk => $vv) {
                echo '<td' . ((strlen($vv) > 1) ? ' class="highlight"' : '') . '>' . (ltrim($vv, "0") ?: '') . '</td>';
                // echo '<td>' . $vv . '</td>';
            }
        }
        echo '</table>';
        echo '<br/>';
    }

    /**
     * Get biggest block in all blocks to solve first
     * 
     */
    private function get_biggest_block()
    {

        $biggest_count = 0;
        foreach ($this->_trim_blocks as $k => $v) {
            foreach ($v as $kk => $vv) {
                if ($biggest_count < count($vv)) {
                    $biggest_count = count($vv);
                    $this->_k1 = $k;
                    $this->_k2 = $kk;
                    $v1 = $vv;
                }
            }
        }

        $this->_block_to_solve[$this->_k1][$this->_k2] = $v1;
        unset($this->_block_WO_biggest_block[$this->_k1][$this->_k2]);

        echo '<br/> block to solve:<br/>';
        print_r($this->_block_to_solve);
        echo '<br/> block without biggest block:<br/>';
        print_r($this->_block_WO_biggest_block);
        echo '<br/> trimmed blocks:<br/>';
        print_r($this->_trim_blocks);
    }

    /**
     * Solve Suduko
     *
     * @return void
     */
    // public function solve()
    // {
    // $this->transpose_blocks();
    // $this->set_blocks();
    // $this->print_sudoku();
    // $this->solve2();

    // // print_r($this->_blocks);
    // print_r($this->_blocks_to_traverse);

    // foreach ($this->_unsolved_sudoku as $k => $v) {
    //     echo '<br>$k = ' . $k . '<br>';
    //     foreach ($v as $kk => $vv) {
    //         echo '<br>$kk = ' . $kk . '<br>';

    //         if ($vv === 0) {
    //             echo '<br>-------------------------Empty cell<br>';
    //             for ($i = 1; $i <= 9; $i++) {
    //                 echo '<br>----------------------------------------------------------------------------------------------------$i = ' . $i . '<br>';

    //                 $valid = true;
    //                 echo '<br>---------------1st check:' . in_array($i, $this->_unsolved_sudoku[$k]) . '<br>';

    //                 if (in_array($i, $this->_unsolved_sudoku[$k])) {
    //                     echo '<br>======= Inside 1st check on: ' . $i . '<br>';
    //                     echo json_encode($this->_unsolved_sudoku[$k]);
    //                     // print_r($this->_unsolved_sudoku[$k]);
    //                     $valid = false;
    //                 }

    //                 echo '<br>---------------2nd check:' . in_array($i, $this->_transposed[$kk]) . '<br>';

    //                 if ($valid && in_array($i, $this->_transposed[$kk])) {
    //                     echo '<br>======= Inside 2nd check on: ' . $i . '<br>';
    //                     echo json_encode($this->_transposed[$kk]);

    //                     // print_r($this->_transposed[$k]);
    //                     $valid = false;
    //                 }
    //                 if ($kk < 3) $block = 1;
    //                 if ($kk > 2) $block = 2;
    //                 if ($kk > 5) $block = 3;

    //                 if ($k < 3) $block_row = 1;
    //                 if ($k > 2) $block_row = 2;
    //                 if ($k > 5) $block_row = 3;
    //                 echo '<br>---------------3rd check:' . in_array($i, $this->_blocks[$block_row][$block]) . '<br>';


    //                 if ($valid && in_array($i, $this->_blocks[$block_row][$block])) {
    //                     echo '<br>======= Inside 3rd check on: ' . $i . '<br>';
    //                     echo json_encode($this->_blocks[$block_row][$block]);

    //                     // print_r($this->_blocks[$block_row][$block]);
    //                     $valid = false;
    //                 }

    //                 if ($valid) {
    //                     $this->_unsolved_sudoku[$k][$kk] = '0' . $i;
    //                     $this->transpose_blocks();
    //                     $this->set_blocks();
    //                     $this->print_sudoku();
    //                     break;
    //                 }
    //                 // if ($k > 1) {
    //                 //     break 3;
    //                 // }
    //             }
    //         } else {
    //             echo '<br>-------------------------Not an empty cell<br>';
    //         }
    //     }
    // }

    //get the biggest block first
    // $bb = $this->get_biggest_block();

    // print_r($this->_blocks[$this->_k1][$this->_k2]);

    // foreach ($this->_unsolved_sudoku as $k => $v) {
    // }

    //print sudoku
    // $this->print_sudoku();


    // $this->print_sudoku($this->_transposed);
    // }
}


echo '<pre>';
$unsolved_sudoku = [
    [0, 4, 0, 5, 0, 7, 0, 0, 6],
    [0, 0, 7, 0, 0, 0, 2, 3, 0],
    [2, 0, 6, 1, 0, 3, 0, 0, 5],

    [0, 0, 2, 3, 0, 0, 0, 0, 4],
    [0, 3, 0, 6, 0, 4, 0, 8, 0],
    [6, 0, 0, 0, 0, 2, 1, 0, 0],

    [7, 0, 0, 8, 0, 6, 3, 0, 1],
    [0, 1, 8, 0, 0, 0, 5, 0, 0],
    [9, 0, 0, 7, 0, 1, 0, 2, 0],
];

$sudokuSolver = new Sudoku($unsolved_sudoku);
// $sudokuSolver->solve();
$sudokuSolver->solve2();
