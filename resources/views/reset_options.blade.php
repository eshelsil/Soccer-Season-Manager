@extends('layouts.app')

@section('title', 'Reset')
@section('menu')
    @include('menu', ['view' => 'reset_options'])
@endsection

@section('content')
    <div class="h3 mt-2 mb-4"><u>
        Reset Options
    </u></div>
    @csrf
    <div class="container row">
        @include('button_modal', [
            'wrapper_class' => 'pr-4',
            'button_id' => 'reset_scores',
            'button_label' => 'Reset All Game Scores',
            'title' => 'Reset All Game Scores',
            'msg' => "This will reset the score of all played games. \n Are you sure?",
            'action_label' => 'Reset',
            'cancel_label' => 'Cancel'
        ])
        @include('button_modal', [
            'wrapper_class' => 'pl-4',
            'button_id' => 'reset_games',
            'button_label' => 'Reset Games Schedule',
            'title' => 'Reset All Games From Schedule',
            'msg' => "This will the schedule of all games. \n
                        It means that the only remained data will be the list of teams and you'll have to set all games schedule again. \n
                        Are you sure?",
            'action_label' => 'Reset',
            'cancel_label' => 'Cancel'
        ])
    </div>
@endsection