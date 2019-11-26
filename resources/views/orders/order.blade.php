@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="../../resources/sass/app.scss">
<script type="text/javascript" src="../../resources/js/app.js"></script>

@section('content')
    <a href="../orders">Back</a>
    <div class="product-content">
        @if(count($orders) > 0)
        @foreach($orders as $order)

            <h1 class="product-list-header">Order {{$order->order_number}}</h1>
            <ul class="list-group">
                <li class="list-group-item">                
                    <strong class="product-label">Total Price:</strong>$ {{$order->total_price}}
                    <br><br>
                    <h5><strong>Products</strong></h5>
                    @foreach($order->products as $product)
                        <strong class="product-label">Title:</strong>{{$product->title}}
                        <strong class="product-label">Size:</strong>{{$product->size}}
                        <strong class="product-label">Price:</strong>$ {{$product->price}}
                        <strong class="product-label">Quantity:</strong>{{$product->quantity}}
                        <br>

                    @endforeach
                </li>
            </ul>
        @endforeach
        @else
            <p>no orders</p>
        @endif
    </div>
    <a href="../prints" class="btn btn-outline-secondary directory-buttons">Back to Prints</a>

@endsection