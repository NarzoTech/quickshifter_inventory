@extends('accounts::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('accounts.name') !!}</p>
@endsection
