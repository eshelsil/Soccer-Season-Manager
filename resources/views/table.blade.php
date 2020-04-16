@extends('layouts.app')

@section('title', 'Table')

@section('menu')
    @include('snippets.menu', ['view' => 'table'])
@endsection

@section('content')
    @if (!is_null($last_week))
    <div class="col mb-4">
        @include('snippets.select_input', [
            'id' => 'weekSelect',
            'label' => 'Until Week',
            'with_all_option' => true,
            'initial_value' => $query_params['week'] ?? null,
            'options' => range(1, $last_week)
        ])
    </div>
    @endif

    <div class="h3 mt-2 mb-4"><u>
        Season Table
    </u></div>

    <table class="table table-striped shrunk">
    <thead class="thead-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">Team</th>
            <th scope="col">Points</th>
            <th scope="col">Games</th>
            <th scope="col">W.</th>
            <th scope="col">D.</th>
            <th scope="col">L.</th>
            <th scope="col">GF</th>
            <th scope="col">GA</th>
            <th scope="col">GD</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $table = array();
            foreach(TEAMS_BY_ID as $team_id => $team_name){
                $table[$team_id] = array(
                    'team_id'=>$team_id,
                    'team_name'=>$team_name,
                    'points'=>0,
                    'games'=>0,
                    'wins'=>0,
                    'draws'=>0,
                    'loses'=>0,
                    'goals_for'=>0,
                    'goals_against'=>0
                );
            }
            foreach($games as $game){
                $home_team_id = $game->home_team_id;
                $away_team_id = $game->away_team_id;
                $score_home = $game->home_score;
                $score_away = $game->away_score;

                $table[$home_team_id]['games'] += 1;
                $table[$home_team_id]['goals_for'] += $score_home;
                $table[$home_team_id]['goals_against'] += $score_away;
                $table[$away_team_id]['games'] += 1;
                $table[$away_team_id]['goals_for'] += $score_away;
                $table[$away_team_id]['goals_against'] += $score_home;
                if ($score_home > $score_away){
                    $table[$home_team_id]['wins'] += 1;
                    $table[$home_team_id]['points'] += 3;
                    $table[$away_team_id]['loses'] += 1;
                } elseif ($score_home < $score_away){
                    $table[$away_team_id]['wins'] += 1;
                    $table[$away_team_id]['points'] += 3;
                    $table[$home_team_id]['loses'] += 1;
                } else{
                    $table[$home_team_id]['draws'] += 1;
                    $table[$home_team_id]['points'] += 1;
                    $table[$away_team_id]['draws'] += 1;
                    $table[$away_team_id]['points'] += 1;
                }
            }
            #NOTE move these calculations to frontend

            function cmp($team_a, $team_b){
                $points_a = $team_a['points'];
                $points_b = $team_b['points'];
                {
                    if ($points_a != $points_b) {
                        return ($points_a > $points_b) ? -1 : 1;
                    }
                    $gf_a = $team_a['goals_for'];
                    $ga_a = $team_a['goals_against'];
                    $gd_a = $gf_a - $ga_a;
                    $gf_b = $team_b['goals_for'];
                    $ga_b = $team_b['goals_against'];
                    $gd_b = $gf_b - $ga_b;
                    if ($gd_a != $gd_b){
                        return ($gd_a > $gd_b) ? -1 : 1;
                    }
                    if ($gf_a != $gf_b){
                        return ($gf_a > $gf_b) ? -1 : 1;
                    }
                    return 0;
                }
            }
            usort($table, 'cmp');
            #NOTE todo: equal teams by inner-games
            $leader_class = "font-weight-bold";
        ?>
        @foreach ($table as $index => $team_data)
            @php
                $rank = $index + 1;
                $team_id = $team_data['team_id'];
                $team_name = $team_data['team_name'];
                $points = $team_data['points'];
                $games = $team_data['games'];
                $wins = $team_data['wins'];
                $draws = $team_data['draws'];
                $loses = $team_data['loses'];
                $gf = $team_data['goals_for'];
                $ga = $team_data['goals_against'];
                $gd = $gf - $ga;
            @endphp
            <tr class='{{$index == 0 ? $leader_class : null}}'>
                <td class='shrunk'>{{$rank}}</td>
                <td class='shrunk'><a href='/games?team_id={{$team_id}}'>{{$team_name}}</a></td>
                <td class='shrunk'>{{$points}}</td>
                <td class='shrunk'>{{$games}}</td>
                <td class='shrunk'>{{$wins}}</td>
                <td class='shrunk'>{{$draws}}</td>
                <td class='shrunk'>{{$loses}}</td>
                <td class='shrunk'>{{$gf}}</td>
                <td class='shrunk'>{{$ga}}</td>
                <td class='shrunk'>{{$gd}}</td>
            </tr>
        @endforeach
    </tbody>
    </table>
@endsection
