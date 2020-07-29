@extends('layouts.app')

@section('title', 'Games')

@section('menu')
    @include('snippets.main_menu', ['view' => 'games'])
@endsection

@section('content')
    @if($has_games)

    @php
        $init_options = [
            'teams_by_id' => $teams_by_id
        ];
    @endphp

    <div ng-controller="games_display" ng-init='initialize(@json($init_options))'>
    <div class="row justify-content-start m-0">
        @include('snippets.table_filters', [
            'filters' => ['team', 'round', 'week'],
            'team_attrs' => [
                'label' => 'Choose Team'
            ],
            'round_attrs' => [
                'label' => 'Choose Round'
            ],
            'week_attrs' => [
                'label' => 'Choose Week'
            ]
        ])
    </div>

        <div class="h3 mt-2 mb-4"><u>
            <p ng-show="is_team_selected()">Games of @{{teams_by_id[team_filter]}}</p>
            <p ng-hide="is_team_selected()">Games</p>
        </u></div>


    <table class="table table-striped shrunk">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Week</th>
                <th scope="col">Time</th>
                <th ng-show="is_team_selected()" scope="col">Res.</th>
                <th scope="col">Home Team</th>
                <th scope="col">Away Team</th>
                <th colspan="3" scope="col">Score</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="game in games">
                <td class='shrunk'>@{{game.week}}</td>
                <td class='date_cell'>@{{print_time_cell(game.time)}}</td>
                <td ng-show="is_team_selected()" ng-class="{
                    'text-success': game.team_result == 'win',
                    'text-danger': game.team_result == 'lose',
                }">@{{get_shorten_result(game.team_result)}}</td>
                <td class="shrunk" ng-class="{'selected_team_cell': game.team_side=='home', 'winner_cell': game.is_home_winner}">@{{game.home_team_name}}</td>
                <td class="shrunk" ng-class="{'selected_team_cell': game.team_side=='away', 'winner_cell': game.is_away_winner}">@{{game.away_team_name}}</td>
                <td class='shrunk pr-0' ng-class="{'winner_cell': game.is_home_winner}">@{{game.home_score}}</td>
                <td class='shrunk pr-0 pl-0'>@{{game.is_done ? ':' : ''}}</td>
                <td class='shrunk pl-1' ng-class="{'winner_cell': game.is_away_winner}">@{{game.away_score}}</td>
            </tr>
        </tbody>
    </table>

    @else
        <div class="h3 mt-2">
            There are no scheduled games yet
        </div>
    @endif
@endsection
