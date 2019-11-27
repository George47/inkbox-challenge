@extends('layouts.app')

@section('content')
    @if(count($orders) > 0)
        <p>Orders to process:</p>
        @foreach($orders as $order)
            <p><a href="orders/{{$order->order_number}}">{{$order->order_number}}</a></p>
        @endforeach
    @endif
    <button id="generatePrint" class="btn btn-outline-secondary directory-buttons">Generate Print</button>
    {{-- <div class="print-report"></div> --}}
    <div class="container">
        <div class="row print-report">
        </div>
    </div>
@endsection