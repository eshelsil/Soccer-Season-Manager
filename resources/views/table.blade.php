@extends('layouts.app')

@section('title', 'Page Title')

@section('menu')
    @include('menu', ['view' => 'table'])
@endsection

@section('content')
    TABLE
@endsection