@extends('layouts.app')

@section('content')
    <div class="product-content">
        <h1 class="product-list-header">Products</h1>
        @if(count($products) > 1)
            <ul class="list-group">
            @foreach($products as $product)
                <li class="list-group-item">
                    <strong class="product-label">Name:</strong>{{$product->title}}
                    <strong class="product-label">Size:</strong>{{$product->size}} 
                    <strong class="product-label">SKU:</strong>{{$product->sku}}
                    <strong class="product-label">Price:</strong>{{$product->price}}
                </li>
                {{-- <div class="well">
                    <h3>{{$product->title}}, {{$product->size}}, {{$product->sku}}, {{$product->price}}</h3>
                </div> --}}
            @endforeach
            </ul>
        @else
            <p>no products</p>
        @endif
    </div>
@endsection