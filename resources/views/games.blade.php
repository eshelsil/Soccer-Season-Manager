@extends('layouts.app')

@section('title', 'Page Title')

@section('menu')
    @include('menu', ['view' => 'games'])
@endsection

@section('content')
    GAMES

    {{-- @json($games, JSON_PRETTY_PRINT); --}}

    <table class="table table-striped shrunk">
        <thead class="thead-light">
            <tr>
                <th scope="col">Round</th>
                <th scope="col">Week</th>
                <th scope="col">Home Team</th>
                <th scope="col">Away Team</th>
                <th colspan="3" scope="col">Score</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach($games as $game){
                $round = $game->round;
                $week = $game->week;
                $home_team_name = TEAMS_BY_ID[$game->home_group_id];
                $away_team_name = TEAMS_BY_ID[$game->away_group_id];
                $score_home = $game->home_score;
                $score_away = $game->away_score;
                $away_bold_class = '';
                $home_bold_class = '';
                $score_separator = ':';
                if ($score_home > $score_away){
                    $home_bold_class = 'font-weight-bold';
                } elseif ($score_home < $score_away) {
                    $away_bold_class = 'font-weight-bold';
                }
                $is_done = $game->is_done;
                if (!$is_done){
                    $score_separator = '';
                }
                echo "
                <tr>
                    <td class='shrunk'>$round</td>
                    <td class='shrunk'>$week</td>
                    <td class='shrunk $home_bold_class'>$home_team_name</td>
                    <td class='shrunk $away_bold_class'>$away_team_name</td>
                    <td class='shrunk pr-0 $home_bold_class'>$score_home</td>
                    <td class='shrunk pr-0 pl-0'>$score_separator</td>
                    <td class='shrunk pl-1 $away_bold_class'>$score_away</td>
                </tr>
                ";
            }
            ?>
        </tbody>
    </table>
@endsection