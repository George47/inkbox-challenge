@extends('layouts.app')

@section('content')
    <div class="product-content">
        <h1 class="product-list-header">Orders</h1>
        @if(count($orders) > 0)
            <ul class="list-group">
            @foreach($orders as $order)
                <li class="list-group-item">
                    <strong class="product-label">Order Number:</strong>{{$order->order_number}}
                    <strong class="product-label">Total Price:</strong>{{$order->total_price}}
                </li>
            @endforeach
            </ul>
        @else
            <p>no orders</p>
        @endif
    </div>
@endsection