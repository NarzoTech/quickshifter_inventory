@extends('supplier::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('supplier.name') !!}</p>
@endsection
