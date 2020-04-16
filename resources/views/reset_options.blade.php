@extends('layouts.app')

@section('title', 'Reset')
@section('menu')
    @include('snippets.menu', ['view' => 'reset_options'])
@endsection

@section('content')
    <div class="h3 mt-2 mb-4"><u>
        Reset Options
    </u></div>
    <div class="row ml-0">
        @include('snippets.button_modal', [
            'button_id' => 'reset_scores',
            'button_label' => 'Reset All Game Scores',
            'title' => 'Reset All Game Scores',
            'msg' => "This will reset the score of all played games. \n Are you sure?",
            'action_label' => 'Reset',
            'cancel_label' => 'Cancel'
            ])
    </div>
    <div class="row ml-0 mt-5">
        @include('snippets.button_modal', [
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
