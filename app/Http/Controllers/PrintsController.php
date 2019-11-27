<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        $matrix = $this->matrix;
        $seen = $this->seen;

        $orders = $this->orders;

        // gather products
        $products = $this->gatherProducts($orders);

        // change sorting of products to here,
        // create two possible algorithms, one sorted and one randomly generates all possible combinations

        
        // create matrix
        $matrix = $this->processOrders($products);

        // $matrix = $this->insertGrid($products);

        return $matrix;
    }

    private function sortByHeight($a, $b)
    {
        return $b->height - $a->height;
    }

    private function gatherProducts($orders)
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

            usort($order_details->products, array($this, "sortByHeight")); 

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
        $order_ids = array();

        $print_sheets = array();

        $matrix = $this->initiatePrint($this->row_len, $this->col_len, '0');    

        
        $index = 0;
        print_r($orders);die;
        // while(!empty($orders))
        // {
        //     // $orders[$index]

        //     // if inserted, set index to 0 again
        // }
        // \unset($orders[0]->products[1]);
        foreach ($orders as $order_details)
        {

            $process_order = $order_details->order_id;
            $process_products = new \ArrayObject($order_details->products);

            $insert_result = $this->insertProducts($matrix, $process_products);

            if ($insert_result['success'])
            {
                //record saved matrix
                $matrix = $insert_result['matrix'];

                // remove order from order list
                unset($orders[$index]);
                
                continue;
            } else {
                // print_r($process_order . 'failed');die;
                //@TODO:
                //  keep order, next order
                //      if index at end of order list
                //          make new matrix 
                //          reset index
            }
        }

        //@TODO: save all matrix
        $print_sheets[] = $matrix;
        return $print_sheets;
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

        foreach($products as $product)
        {
            $result = $this->insertProduct($matrix, $product);
            // print_r($result);
            // success insertion, continue to next product
            if ($result['success'])
            {
                $matrix = $result['matrix'];
            } else {
                // one of the products failed to insert, report failure
                return array('success'=>false, 'matrix'=>$matrix_clone);
            }
        }

        return array('success'=>true, 'matrix'=>$matrix);
    }

    // while loop to inject items for order,
    // clone products and matrix at beginning of function
    // if all inserted, return new matrix and mark order processed
    // if cannot all be inserted, return old matrix and leave order to be process for next 

    // dynamic programming could be implemented as well as an improvement
    private function insertGrid($products)
    {
        $print_sheets = array();

        // print_r(json_encode($products));die;
        // initiate empty matrix
        $matrix = $this->initiatePrint($this->row_len, $this->col_len, '0');

        // record inserted products
        $inserted_products = new \ArrayObject(); 

        // run algorithm
        foreach ($products as $product_list)
        {
            // record products, can just record order details product id
            $products_clone = array();
            // $products_clone = new \ArrayObject($product_list->products);

            foreach($product_list->products as $product)
            {

                // START INSERT
                $matrix = $this->insertProduct($matrix, $product)['matrix'];
                // END INSERT
            }
            
            // $product_list->products[] = $product_list->products[1];
            // $diff = array_diff(
            //     $product_list->products,
            //     $products_clone
            // );
            // print_r($diff);die;
        }

        // save the sheet
        $current_stamp = time();
        $fp = fopen('./archive/'.time().'.csv', 'w');

        foreach ($matrix as $fields) {
            fputcsv($fp, $fields);
        }
        
        fclose($fp);
        // print_r($matrix);die;
        $this->savePrint($current_stamp, $inserted_products);

        $print_sheets[] = $matrix;
        return $print_sheets;
    }

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
                    // echo 'doing ' .$product->height . 'x' . $product->width . "\n";

                    // check if submatrix in in matrix
                    if (($row + $product->height <= count($matrix)) && ($col + $product->width <= count($matrix[0])))
                    {
                        // clone matrix
                        $matrix_clone = new \ArrayObject($matrix);

                        // get sub matrix
                        $sub_matrix = new \ArrayObject();

                        // echo 'row at ' . $row . ', col at ' . $col . "\n";
                        for ($height = $row; $height < $row + $product->height; $height++)
                        {
                            $sub_matrix[] = array_splice($matrix_clone[$height], $col, $product->width);
                        }

                        // print_r($sub_matrix);

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
                                    // print_r($inserted_product->order_item_id . ' INSERTED AS '.$product->name.', AT ' .$i.'x'.$j. "\n");
                                    $matrix[$i][$j] = $product->name;
                                    $inserted = true;
                                }
                            }

                            $inserted_products[] = $inserted_product;

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
        return array('success'=>true, 'matrix'=>$matrix);
    }

    /*
    *   Backup function to be developed for mixed orders
    *   @TODO: change for mixed orders
    */
    private function gatherProductsMixed($orders)
    {
        $products = array();

        // change the way to gather products
        foreach ($orders as $order)
        {
            foreach($order->products as $order_product)
            {
                $productSizeRaw = $order_product->size;
                $productSize = explode('x', $productSizeRaw);

                for ($i = 0; $i < $order_product->quantity; $i++)
                {
                    $product = new \stdClass;
                    $product->name = $order_product->title[0];
                    $product->width = $productSize[0];
                    $product->height = $productSize[1];
                    $product->order_item_id = $order_product->order_item_id;
                    $products[] = $product;    
                }
            }
        }

        usort($products, array($this, "sortByHeight")); 

        return $products;
    }

    /*
    *   Backup function to be developed for mixed orders
    *   @TODO: change for mixed orders
    */
    private function insertGridMixed($products)
    {
        // initiate empty matrix
        $matrix = $this->initiatePrint($this->row_len, $this->col_len, '0');

        // record inserted products
        $inserted_products = new \ArrayObject(); 

        // run algorithm
        foreach ($products as $product)
        {
            for ($row = 0; $row < count($matrix); $row++)
            {
                for ($col = 0; $col < count($matrix[0]); $col++)
                {
                    if ($matrix[$row][$col] === '0')
                    {
                        $inserted = false;

                        // echo 'doing ' .$product->height . 'x' . $product->width . "\n";

                        // check if submatrix in in matrix
                        if (($row + $product->height <= count($matrix)) && ($col + $product->width <= count($matrix[0])))
                        {
                            // clone matrix
                            $matrix_clone = new \ArrayObject($matrix);

                            // get sub matrix
                            $sub_matrix = new \ArrayObject();

                            // echo 'row at ' . $row . ', col at ' . $col . "\n";
                            for ($height = $row; $height < $row + $product->height; $height++)
                            {
                                $sub_matrix[] = array_splice($matrix_clone[$height], $col, $product->width);
                            }

                            // print_r($sub_matrix);

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

                                        // 

                                        // Warning: #1366 Incorrect integer value: '' for column 'ps_id' at row 1

                                    }
                                }

                                $inserted_products[] = $inserted_product;

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

        // save the sheet
        $current_stamp = time();
        $fp = fopen('./archive/'.time().'.csv', 'w');

        foreach ($matrix as $fields) {
            fputcsv($fp, $fields);
        }
        
        fclose($fp);
        // print_r($matrix);die;
        $this->savePrint($current_stamp, $inserted_products);

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
