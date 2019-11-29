<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Prints;
use App\PrintItem;

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
        // should cover size overflow issue

        $this->matrix = $this->initiatePrint($this->row_len, $this->col_len, '0');    
        $this->seen = $this->initiatePrint($this->row_len, $this->col_len, false);    

        $this->orders = app('App\Http\Controllers\OrdersController')->getOrders();
    }

    private function initiatePrint($row_len, $col_len, $identifier)
    {
        return array_fill(0, $col_len, array_fill(0, $row_len, $identifier));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        // return $this->getUnusedArea($matrix);
        return view('prints.index')->with('orders', $this->orders);
    }

    // approach:
    //
    // order level insertion
    // better solutions
    //      search for optimized row
    //      dynamic programming for optimized
    //


    /**
     * Showing response of print sheet.
     *
     * @return \Illuminate\Http\Response
     */
    public function generatePrint()
    {
        // print_r($_GET);die;
        $matrix = $this->matrix;
        $seen = $this->seen;

        $orders = $this->orders;

        $products_full = array();

        // gather products
        $products_full[] = $this->gatherProducts($orders);
        $products_full[] = $this->gatherProducts($orders, 'sortByWidth');
        $products_full[] = $this->gatherProducts($orders, 'sortByHeight');


        $shuffle_count = 50;
        if (isset($_GET['shuffle']))
        {
            $shuffle_count = $_GET['shuffle'];
        }

        // change shuffle count depending on count($orders)

        for ($shuffle=0; $shuffle < $shuffle_count; $shuffle++)
        {
            $products_full[] = $this->gatherProducts($orders, 'shuffle');
        }

        // @TODO: alternative, find all possible combinations of order placement and products in order

        $sheets_full = array();

        // create matrix
        foreach ($products_full as $products)
        {
            $sheets_full[] = $this->processOrders($products);
        }

        usort($sheets_full, array($this, 'sortByUnused'));
// print_r($sheets_full);die;
        $response_sheets = $sheets_full[0];

        $current_stamp = time();
        for ($i = 0; $i < count($response_sheets['print']); $i++)
        {
            // save sheets
            $fp = fopen('./archive/'.$current_stamp.'-'.(string)$i.'.csv', 'w');

            foreach ($response_sheets['print'][$i] as $fields) {
                fputcsv($fp, $fields);
            }

            $in_sheet_products = array();

            foreach ($response_sheets['products'] as $in_sheet_product)
            {
                if ($in_sheet_product->sheet_num === $i)
                {
                    $in_sheet_products[] = $in_sheet_product;
                }
            }

            $this->savePrint(($current_stamp.'-'.(string)$i), $in_sheet_products);
    
            fclose($fp);
        }

        // return new JsonResponse(array('unused' => $sheets_full[0]['unused'], 'sheet' => $sheets_full[0]['print']));
        // return Response::json(array('unused' => $sheets_full[0]['unused'], 'sheet' => $sheets_full[0]['print']));
        return Response::json(['unused' => $sheets_full[0]['unused'], 'sheet' => $sheets_full[0]['print']]);

    }

    private function sortByUnused($a, $b)
    {
        return $a['unused'] - $b['unused'];
    }

    private function sortByHeight($a, $b)
    {
        return $b->height - $a->height;
    }

    private function sortByWidth($a, $b)
    {
        return $b->width - $a->width;
    }

    private function gatherProducts($orders, $sortBy=false)
    {
        $products = array();

        // change the way to gather products
        foreach ($orders as $order)
        {

            // initiate order details with product
            $order_details = new \stdClass;
            $order_details->order_id = $order->order_id;
            $order_details->products = array();
            $order_details->size = 0;

            foreach($order->products as $order_product)
            {
                $productSizeRaw = $order_product->size;
                $productSize = explode('x', $productSizeRaw);

                for ($i = 0; $i < $order_product->quantity; $i++)
                {

                    $product = new \stdClass;
                    $product->id = NULL;
                    $product->name = $order_product->title[0];
                    $product->width = $productSize[0];
                    $product->height = $productSize[1];
                    $product->order_item_id = $order_product->order_item_id;

                    $order_details->size += (int) $productSize[0] * (int) $productSize[1];
                    
                    $order_details->products[] = $product;
                }
            }

            
            if ($sortBy)
            {
                if ($sortBy === 'shuffle')
                {
                    shuffle($order_details->products);
                } else {
                    usort($order_details->products, array($this, $sortBy)); 
                }
            }

            for ($j = 0; $j < count($order_details->products); $j++)
            {
                $order_details->products[$j]->id = $j;
            }

            $products[] = $order_details;
        }

        return $products;
    }


    // get list of products, sort by sizing from high to low,
    // then get all possible combinations and 
    // dfs on 0s and get all combination

    private function processOrders($orders)
    {
        $print_sheets = array();

        $matrix = $this->initiatePrint($this->row_len, $this->col_len, '0');    
        $sheet_num = 0;

        $index = 0;

        $inserted_products = array();

        while(count($orders) > 0)
        {
            $process_order = $orders[$index]->order_id;
            $process_products = new \ArrayObject($orders[$index]->products);

            $insert_result = $this->insertProducts($matrix, $process_products);

            if ($insert_result['success'])
            {
                //record saved matrix
                $matrix = $insert_result['matrix'];
                
                // $insert_result['product']->sheet_num = $sheet_num;
                foreach ($insert_result['product'] as $sheet_product)
                {
                    $sheet_product->sheet_num = $sheet_num;
                }

                $inserted_products[] = $insert_result['product'];
                
                // issue here
                if ($index === (count($orders) - 1))
                {
                    unset($orders[$index]);
                    $orders = array_values($orders);
                    $index = 0;
                    continue;
                }

                unset($orders[$index]);
                $orders = array_values($orders);
                continue;

            } else {
                //@TODO:
                if ($index === (count($orders) - 1))
                {
                    $print_sheets[] = $matrix;
                    
                    $matrix = $this->initiatePrint($this->row_len, $this->col_len, '0');
                    $sheet_num += 1;

                    $index = 0;
                    continue;

                } else {
                    $index++;
                }
                //  keep order, next order
                //      if index at end of order list
                //          make new matrix 
                //          reset index
            }



        }

        // save last used matrix
        // $print_sheets[] = $this->getUnusedArea($matrix);
        $print_sheets[] = $matrix;
        
        $unused = 0;
        foreach ($print_sheets as $print_sheet)
        {
            $unused += $this->getUnusedArea($print_sheet);
        }

        $result = array(
            'unused' => $unused,
            'print' => $print_sheets,
            // 'products' => $inserted_products
            'products' => call_user_func_array('array_merge', $inserted_products)
        );

        return $result;
    }

    // insert each product to matrix, and unset product
    // if product list is empty after ending array
    //      return inserted = true and new matrix
    // else
    //      return inserted = false and original matrix
    private function insertProducts(&$matrix, $products)
    {
        //original matrix
        $matrix_clone = new \ArrayObject($matrix);

        $inserted_products = array();
        foreach($products as $product)
        {
            $result = $this->insertProduct($matrix, $product);

            // success insertion, continue to next product
            if ($result['success'])
            {
                $matrix = $result['matrix'];
                $inserted_products[] = $result['product'];
            } else {
                // one of the products failed to insert, report failure
                return array('success'=>false, 'matrix'=>$matrix_clone);
            }
        }

        return array('success'=>true, 'matrix'=>$matrix, 'product'=>$inserted_products);
    }

    // while loop to inject items for order,
    // clone products and matrix at beginning of function
    // if all inserted, return new matrix and mark order processed
    // if cannot all be inserted, return old matrix and leave order to be process for next 

    private function insertProduct(&$matrix, $product)
    {
        $original_matrix = new \ArrayObject($matrix);
        $inserted = false;

        for ($row = 0; $row < count($matrix); $row++)
        {
            for ($col = 0; $col < count($matrix[0]); $col++)
            {
                if ($matrix[$row][$col] === '0')
                {
                    //@TODO: consider case of 2x5 and 5x2 fit swap

                    // check if submatrix in in matrix
                    if (($row + $product->height <= count($matrix)) && ($col + $product->width <= count($matrix[0])))
                    {
                        // clone matrix
                        $matrix_clone = new \ArrayObject($matrix);

                        // get sub matrix
                        $sub_matrix = new \ArrayObject();

                        for ($height = $row; $height < $row + $product->height; $height++)
                        {
                            $sub_matrix[] = array_splice($matrix_clone[$height], $col, $product->width);
                        }

                        // if rectangle can be injected
                        if ($this->injectable($sub_matrix, '0'))
                        {
                            // gather inserted item data at initial
                            $inserted_product = new \stdClass;
                            $inserted_product->x_pos = $col;
                            $inserted_product->y_pos = $row;
                            $inserted_product->width = $product->width;
                            $inserted_product->height = $product->height;
                            $inserted_product->order_item_id = $product->order_item_id;

                            // insert rectangle
                            for ($i = $row; $i < $row + $product->height; $i++)
                            {
                                for ($j = $col; $j < $col + $product->width; $j++)
                                {
                                    $matrix[$i][$j] = $product->name;
                                    $inserted = true;
                                }
                            }

                            $inserted_products = $inserted_product;

                            $products_clone[] = $inserted_product;

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

        // product cannot be inserted, return the original matrix
        if (!$inserted)
        {
            return array('success'=>false, 'matrix'=>$original_matrix);
        }

        return array('success'=>true, 'matrix'=>$matrix, 'product'=>$inserted_products);
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

    private function getUnusedArea($matrix)
    {
        $area = 0;
        $identifier = '0';
        
        for ($row = 0; $row < count($matrix); $row++)
        {
            for ($col = 0; $col < count($matrix[0]); $col++)
            {
                if ($matrix[$row][$col] === $identifier)
                {
                    $area++;
                }
            }
        }

        return $area;
    }


    private function inMatrix($row, $col, &$matrix, &$seen)
    {

        return ($row >= 0) && ($col >= 0) && ($row < count($matrix)) && ($col < count($matrix[0])) && (!isset($seen[$row][$col]));
    }

    public function savePrint($time_stamp, $inserted_products)
    {
        $data = new Request;

        $data['type'] = 'test';
        $data['sheet_url'] = "/public/archive/$time_stamp.csv";
        $data['inserted_items'] = $inserted_products;
        
        $this->store($data);
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
        $data = $request->all();

        // save print sheet
        $print = new Prints;
        $print->type = (string) $data['type'];
        $print->sheet_url = (string) $data['sheet_url'];
        $print->save();

        // save print sheet items
        foreach ($data['inserted_items'] as $item)
        {
            $print_item = new PrintItem;
            $print_item->ps_id = $print->id;
            $print_item->order_item_id = $item->order_item_id;
            $print_item->image_url = '';
            $print_item->size = $item->width . 'x' . $item->height;
            $print_item->x_pos = $item->x_pos;
            $print_item->y_pos = $item->y_pos;
            $print_item->width = $item->width;
            $print_item->height = $item->height;
            $print_item->identifier = 'item';
            $print_item->save();
        }

        // return redirect('/prints')->with('success', 'Print Saved!');

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
