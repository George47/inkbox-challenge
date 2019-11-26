<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\OrderItem;
use App\Product;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('orders.index')->with('orders', $this->getOrders());
    }

    public function getOrder($order_id)
    {
        return view('orders.order')->with('order', $this->getOrders($order_id)[0]);
    }

    public function getOrders($order_id = false)
    {
        if (!empty($order_id))
        {
            $orders = Order::get()->where('order_number', $order_id);
        } else {
            $orders = Order::all();
        }
        
        foreach ($orders as $order)
        {
            // append products to order
            $order_products = new \ArrayObject;

            $orders_items = OrderItem::all()->where('order_id', $order->order_id);
            
            foreach ($orders_items as $orders_item)
            {
                $product = Product::where('product_id', $orders_item->product_id)->first();
                $product->quantity = $orders_item->quantity;
                $order_products[] = $product;
            }

            $order->products = $order_products;
        }

        return $orders;
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
        //
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
