<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrintsController extends Controller
{

    /**
     * Initiate the using matrix using given grid length.
     *
     */
    public function __construct()
    {
        $this->row_len = 10;
        $this->col_len = 15;
        $this->matrix = $this->initiateMatrix($this->row_len, $this->col_len, '0');    
        $this->seen = $this->initiateMatrix($this->row_len, $this->col_len, false);    
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return $this->getUnusedArea($matrix);
        return view('prints.index');
    }

    /**
     * Showing response of print sheet.
     *
     * @return \Illuminate\Http\Response
     */
    public function generatePrint()
    {
        // return response()->json([
        //     'success' => 'yes',
        //   ]);

        $matrix = $this->matrix;
        $seen = $this->seen;

        $this->insertGrid($matrix);

        return $matrix;
    }

    // get list of products, sort by sizing from high to low,
    // then get all possible combinations and 
    // dfs on 0s and get all combination

    // dynamic programming could be implemented as well as an improvement
    private function insertGrid(&$matrix)
    {
        $products = array();

        $productSizeRaw = '4x4';
        $productSize = explode('x', $productSizeRaw);
        
        $product = new \stdClass;
        $product->name = '4';
        $product->width = $productSize[0];
        $product->height = $productSize[1];
        $products[] = $product;

        $product = new \stdClass;
        $product->name = '4';
        $product->width = 4;
        $product->height = 4;
        $products[] = $product;

        $product = new \stdClass;
        $product->name = '3';
        $product->width = 3;
        $product->height = 3;
        $products[] = $product;

        $product = new \stdClass;
        $product->name = '2';
        $product->width = 2;
        $product->height = 2;
        $products[] = $product;

        $product = new \stdClass;
        $product->name = '5';
        $product->width = 2;
        $product->height = 5;
        $products[] = $product;

        $product = new \stdClass;
        $product->name = '4';
        $product->width = 4;
        $product->height = 4;
        $products[] = $product;

        // $product = new \stdClass;
        // $product->name = '3';
        // $product->width = 3;
        // $product->height = 3;
        // $products[] = $product;



        foreach ($products as $product)
        {
            for ($row = 0; $row < count($matrix); $row++)
            {
                for ($col = 0; $col < count($matrix[0]); $col++)
                {
                    if ($matrix[$row][$col] === '0')
                    {
                        echo 'for ' . $product->name . ' currently at ' . $row .'-'. $col . "\n";
                        $inserted = false;

                        // check if submatrix in in matrix
                        if (($row + $product->height <= count($matrix)) && ($col + $product->width <= count($matrix[0])))
                        {
                            // clone matrix
                            $matrix_clone = $matrix;

                            // get sub matrix
                            $sub_matrix = array();

                            for ($height = $row; $height <= $row + $product->height; $height++)
                            {
                                echo 'col index at '. $col . ', and should go to ' . ($col + $product->width);
                                $sub_matrix[] = array_splice($matrix_clone[$height], $col, $col + $product->width);
                                print_r(array_splice($matrix_clone[$height], $col, $col + $product->width));
                            }

                            print_r($sub_matrix);

                            // if rectangle can be injected
                            if ($this->injectable($sub_matrix, '0'))
                            {
                                // @TODO: change to dfs and insert                                        
                                for ($i = $row; $i < $row + $product->height; $i++)
                                {
                                    for ($j = $col; $j < $col + $product->width; $j++)
                                    {
                                        $matrix[$i][$j] = $product->name;
                                        $inserted = true;
                                    }
                                }

                                // if inserted, stop the iteration for current product
                                if ($inserted)
                                {
                                    break 2;
                                }

                            }
                        }
                        // $this->dfs($row, $col, $matrix, $seen);
                    }
                }
            }
        }


        return $matrix;
    }

    /*
    *   Check to see if it's all '0' within submatrix 
    */
    private function injectable($matrix, $identifier)
    {
        for ($i = 0; $i < count($matrix); $i++)
        {
            for ($j = 0; $j < count($matrix[0]); $j++)
            {
                if ($matrix[$i][$j] !== $identifier)
                {
                    return false;
                }
            }
        }   

        return true;
    }

    private function dfs($row, $col, &$matrix, &$seen)
    {
        // product size, keep row and col sizing, when go right row - 1, go left col - 1

        // if ($this->inMatrix($row, $col, $matrix))
        if ($row < 0 || $col < 0 || $row >= count($matrix) || $col >= count($matrix[0]) || $matrix[$row][$col] !== '0')
        {
            // out of index
            return;
        } else {
            // $temp = [];
        // if ($this->inMatrix($row, $col, $matrix, $temp)){
            $matrix[$row][$col] = '1';
            $this->dfs($row + 1, $col, $matrix, $seen); // row - 1
            $this->dfs($row - 1, $col, $matrix, $seen); // row - 1
            $this->dfs($row, $col + 1, $matrix, $seen); // col - 1
            $this->dfs($row, $col - 1, $matrix, $seen); // col - 1

            // $matrix[$row][$col] = $product1Name;
            // $this->dfs($row + 1, $col, $matrix, $product1Row, $product1Col, $product1Name);
            // $this->dfs($row - 1, $col, $matrix, $product1Row, $product1Col, $product1Name);
            // $this->dfs($row, $col + 1, $matrix, $product1Row, $product1Col, $product1Name);
            // $this->dfs($row, $col - 1, $matrix, $product1Row, $product1Col, $product1Name);
        }
    }

    private function getUnusedArea(&$matrix)
    {
        $area = 0;
        $identifier = '0';
        
        for ($row = 0; $row < sizeof($matrix); $row++)
        {
            for ($col = 0; $col < sizeof($matrix[0]); $col++)
            {
                if ($matrix[$row][$col] === $identifier)
                {
                    $area++;
                }
            }
        }

        return $area;
    }

    private function initiateMatrix($row_len, $col_len, $identifier)
    {
        return array_fill(0, $col_len, array_fill(0, $row_len, $identifier));
    }

    private function inMatrix($row, $col, &$matrix, &$seen)
    {

        return ($row >= 0) && ($col >= 0) && ($row < count($matrix)) && ($col < count($matrix[0])) && (!isset($seen[$row][$col]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
