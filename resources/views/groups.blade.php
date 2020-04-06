@extends('layouts.app')

@section('title', 'Page Title')
@section('menu')
    @include('menu', ['view' => 'groups'])
@endsection

@section('content')
    GROUPS
@endsection