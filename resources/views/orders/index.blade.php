@extends('layouts.app')

@section('content')
    <div class="product-content">
        <h1 class="product-list-header">Orders</h1>
        @if(count($orders) > 0)
            <ul class="list-group">
            @foreach($orders as $order)
                <li class="list-group-item">
                    <a href="./orders/{{$order->order_number}}">
                        <strong class="product-label">Order Number:</strong>{{$order->order_number}}
                        <strong class="product-label">Total Price:</strong>{{$order->total_price}}
                        
                        <br><br>
                        <h5><strong>Products</strong></h5>
                        {{-- <ul class="list-group"> --}}
                        @foreach($order->products as $product)
                            <strong class="product-label">Title:</strong>{{$product->title}}
                            <strong class="product-label">Size:</strong>{{$product->size}}
                            <strong class="product-label">Price:</strong>$ {{$product->price}}
                            <strong class="product-label">Quantity:</strong>{{$product->quantity}}
                            <br>
                        @endforeach
                    </a>
                    {{-- </ul> --}}
                </li>
            @endforeach
            </ul>
        @else
            <p>no orders</p>
        @endif
    </div>
@endsection