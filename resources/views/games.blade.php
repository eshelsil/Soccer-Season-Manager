@extends('layouts.app')

@section('title', 'Page Title')

@section('menu')
    @include('menu', ['view' => 'games'])
@endsection

@section('content')
    GAMES
@endsection