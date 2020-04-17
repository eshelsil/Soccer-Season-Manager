@extends('layouts.app')

@section('title', 'Scheduling')

@section('content')
    <div class="h3 mt-2 mb-4"><u>
        Step 2 - Schedule Games
    </u></div>

    <div class="row">

        <div class="col-5 mt-3 pl-0">
            @if (empty($weeks_to_schedule))
                <div class="h4 mb-2">No more games to schedule :)</div>
            @else
                <div class="row-1 mb-3 p-0">
                    <button id="auto_schedule" type="button" class="btn btn-primary">Auto schedule all games</button>
                </div>
                <div class="h4 mb-2"><u>Schedule a game:</u></div>
                <div class="p-3 border border-dark rounded" style="background: #dcf0ff;">
                    <div class="col p-0">
                        <div class="row mb-3  ml-0 mr-0 p-0">
                            <div class="col p-0">
                                <label class="row m-0">Round</label>
                                @php
                                    $teams_count = count(array_keys($teams_by_id));
                                    $games_per_round = $teams_count - 1;
                                    $round = ceil($query_params['set_week'] / $games_per_round);
                                @endphp
                                <input type="text" id ="round_input" maxlength="1" value="{{$round}}" disabled style="width:1rem;">
                            </div>
                            <div class="col m-0 p-0">
                                @include('snippets.select_input', [
                                    'id' => 'setWeekSelect',
                                    'label' => 'Week',
                                    'initial_value' => $query_params['set_week'],
                                    'options' => $weeks_to_schedule
                                ])
                            </div>
                        </div>
                        <div class="row m-0 p-0">
                            @php
                            $team_options = [];
                            foreach($available_teams as $team_id){
                                $team_options[$team_id] = $teams_by_id[$team_id];
                            }   
                            @endphp
                            <div class="col m-0 p-0">
                                @include('snippets.select_input', [
                                    'id' => 'homeTeamSelect',
                                    'label' => 'Home Team',
                                    'key_as_value' => true,
                                    'options' => $team_options
                                ])
                            </div>
                            <div class="col m-0 p-0">
                                @include('snippets.select_input', [
                                    'id' => 'awayTeamSelect',
                                    'label' => 'Away Team',
                                    'initial_value' => array_keys($team_options)[1],
                                    'key_as_value' => true,
                                    'options' => $team_options
                                ])
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center mt-4">
                        <button id="schedule_game_button" type="button" class="btn btn-primary">Add Game</button>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-7 mt-3 pl-2">
            <div class="row-1 mb-3 p-0">
                @if ($has_available_games)
                    <button id="truncate_games_table" type="button" class="btn btn-danger mr-2">Delete all games</button>
                @else
                    <button id="go_to_set_teams" type="button" class="btn btn-secondary mr-2">Back to set teams</button>
                @endif
                @if ($allow_set_scores)
                    <button id="to_set_score" type="button" class="btn btn-success">Continue to set scores</button>
                @endif
            </div>
            <div class="h4 mb-2"><u>Scheduled Games:</u></div>
            <div class="p-2 pl-4 border border-dark rounded" style="background: #dcf0ff;">
                @if (!$has_available_games)
                    <div class="h5 mb-2">There are no scheduled games yet</div>
                @else
                    @include('snippets.table_filters', [
                        'round_param' => $query_params['round'],
                        'week_param' => $query_params['week'],
                        'team_id_param' => $query_params['team_id'],
                        'weeks_count' => $weeks_count,
                        'teams_by_id' => $teams_by_id
                    ])
                    <table class="table table-striped shrunk">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Round</th>
                                <th scope="col">Week</th>
                                <th scope="col">Home Team</th>
                                <th scope="col">Away Team</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($filtered_games as $game)
                                @php
                                    $game_id = $game->game_id;
                                    $round = $game->round;
                                    $week = $game->week;
                                    $home_team_name = $teams_by_id[$game->home_team_id];
                                    $away_team_name = $teams_by_id[$game->away_team_id];
                                @endphp
                                <tr>
                                    <td class='shrunk'>{{$round}}</td>
                                    <td class='shrunk'>{{$week}}</td>
                                    <td class='shrunk'>{{$home_team_name}}</td>
                                    <td class='shrunk'>{{$away_team_name}}</td>
                                    <td class='shrunk'>
                                        <div class='delete_game_btn' data-game_id={{$game_id}}></div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
