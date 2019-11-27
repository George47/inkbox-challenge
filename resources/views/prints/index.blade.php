@extends('layouts.app')

@section('content')
    @if(count($orders) > 0)
        <p>Orders to process:</p>
        @foreach($orders as $order)
            <a href="orders/{{$order->order_number}}">{{$order->order_number}}</a>
        @endforeach
    @endif
    <br>
    <button id="generatePrint" class="btn btn-outline-secondary directory-buttons">Generate Print</button>
    
    <div class="loader" style="display: none;"></div>

    {{-- <div class="print-report"></div> --}}
    <div class="container print-report">
        <div class="row print-report-sheets">
        </div>
    </div>
@endsection