@extends('layouts.app')

@section('title', 'Set Scores')
@section('menu')
    @include('menu', ['view' => 'set_scores'])
@endsection

@section('content')
    <div class="h3 mt-2 mb-4"><u>
        Set Scores
    </u></div>
    @csrf
    {{-- <button id="drop_games_table" type="button" class="btn btn-danger">Drop games table</button> --}}
    <button id="randomize_scores" type="button" class="btn btn-primary">Randomize all non-finished games</button>
    <div class="container col mt-3">
        @if (count($unplayed_games) === 0)
            <div class="h5 mb-2">All games are done</div>
        @else
            <div class="h5 mb-2">Non Played Games:</div>
            <table class="table table-striped shrunk">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Round</th>
                        <th scope="col">Week</th>
                        <th scope="col">Home Team</th>
                        <th scope="col">Away Team</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($unplayed_games as $game){
                        $round = $game->round;
                        $week = $game->week;
                        $home_team_name = $teams_by_id[$game->home_team_id];
                        $away_team_name = $teams_by_id[$game->away_team_id];
                        echo "
                        <tr>
                            <td class='shrunk'>$round</td>
                            <td class='shrunk'>$week</td>
                            <td class='shrunk'>$home_team_name</td>
                            <td class='shrunk'>$away_team_name</td>
                        </tr>
                        ";
                    }
                    ?>
                </tbody>
            </table>
        @endif
    </div>
@endsection