@extends('layouts.app')

@section('title', 'Scheduling')

@php
    $init_options = [
        'teams_by_id' =>$teams_by_id,
        'weeks_count'=>$weeks_count
    ];
@endphp
@section('content')
    <div class="h3 mt-2 mb-4"><u>
        Step 2 - Schedule Games
    </u></div>

    <div class="row" ng-controller="games_scheduler" ng-init='initialize(@json($init_options));'>
        
        @if ($can_schedule_games)
        <div class="col-5 mt-3 pl-0">
            <div ng-show="count_games() >= {{$games_in_season}}" class="h4 mb-2">No more games to schedule :)</div>
            <div ng-show="count_games() < {{$games_in_season}}">
                <div class="row-1 mb-3 p-0">
                    <button ng-click="auto_schedule_all()" type="button" class="btn btn-primary">Auto schedule all games</button>
                </div>
                <div class="h4 mb-2"><u>Schedule a game:</u></div>
                <div class="p-3 border border-dark rounded" style="background: #dcf0ff;">
                    <div class="col p-0">
                        <div class="row mb-3  ml-0 mr-0 p-0">
                            <div class="col p-0">
                                <label class="row m-0">Round</label>
                                <input type="text" id="round_input" maxlength="1" value="@{{get_round_input()}}" disabled style="width:1rem;">
                            </div>
                            <div class="col m-0 p-0">
                                @include('snippets.select_input', [
                                    'ng_model' => 'set_week_input',
                                    'options_var' => 'week_input_options',
                                    'id' => 'setWeekSelect',
                                    'label' => 'Week',
                                ])
                            </div>
                        </div>
                        <div class="row m-0 p-0">
                            <div class="col m-0 p-0">
                                @include('snippets.select_input', [
                                    'ng_model' => 'home_team_input',
                                    'options_var' => 'home_team_options',
                                    'id' => 'homeTeamSelect',
                                    'label' => 'Home Team',
                                ])
                            </div>
                            <div class="col m-0 p-0">
                                @include('snippets.select_input', [
                                    'ng_model' => 'away_team_input',
                                    'options_var' => 'away_team_options',
                                    'id' => 'awayTeamSelect',
                                    'label' => 'Away Team',
                                ])
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center mt-4">
                        <button type="button" class="btn btn-primary" ng-click="add_game()">Add Game</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-7 mt-3 pl-2">
            <div class="row-1 mb-3 p-0">
                <button ng-show="has_games()" ng-click="remove_all_games()" type="button" class="btn btn-danger mr-2">Delete all games</button>
                <button ng-show="!has_games()" ng-click="go_to_set_teams()" type="button" class="btn btn-secondary mr-2">Back to set teams</button>
                <button ng-show="count_games() >= {{$games_in_season}}" ng-click="go_to_set_scores()" type="button" class="btn btn-success">Continue to set scores</button>
            </div>
            <div class="h4 mb-2"><u>Scheduled Games:</u></div>
            <div class="p-2 pl-4 border border-dark rounded" style="background: #dcf0ff;">
                <div ng-show="!has_games()" class="h5 mb-2">There are no scheduled games yet</div>
                <div ng-show="has_games()">
                    @include('snippets.table_filters')
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
                            <tr ng-repeat="game in filtered_games">
                                <td class='shrunk'>@{{game.round}}</td>
                                <td class='shrunk'>@{{game.week}}</td>
                                <td class='shrunk' ng-class="{'selected_team_cell': game.home_team_id==team_filter}">@{{game.home_team_name}}</td>
                                <td class='shrunk' ng-class="{'selected_team_cell': game.away_team_id==team_filter}">@{{game.away_team_name}}</td>
                                <td class='shrunk'>
                                    <div class='delete_game_btn' ng-click="remove_game(game.id)"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @else
        <div class="col">
            <p class="mb-2">
                There are currently {{count($teams_by_id)}} registered teams.
                <br>
                In order to start scheduling games there must be even number of teams and at least 4 teams.
            </p>
            <button ng-click="go_to_set_teams()" type="button" class="btn btn-secondary mt-2">Back to set teams</button>
        </div>
        @endif
    </div>
@endsection
