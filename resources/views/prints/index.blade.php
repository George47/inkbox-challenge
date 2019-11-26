@extends('layouts.app')

@section('content')
    @if(count($orders) > 0)
        <p>Orders to process:</p>
        @foreach($orders as $order)
            <p>{{$order->order_number}}</p>
        @endforeach
    @endif
    <button id="generatePrint" class="btn btn-outline-secondary directory-buttons">Generate Print</button>
    <div class="print-report"></div>
@endsection