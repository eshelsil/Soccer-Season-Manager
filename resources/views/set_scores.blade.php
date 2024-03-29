@php
    $init_options = [
      'teams_by_id' => $teams_by_id
    ];
@endphp
@extends('layouts.card', [
  'cards' => [
    [
      'url' => '/admin/scores?'. http_build_query( array_merge($_GET, ['is_done' => 0]) ),
      'label' => 'Non Played Games',
      'active' => !$is_on_done_tab,
    ],
    [
      'url' => '/admin/scores?'. http_build_query( array_merge($_GET, ['is_done' => 1]) ),
      'label' => 'Played Games',
      'active' => $is_on_done_tab,
    ]
  ]
])

@section('title', 'Set Scores')
@section('container', 'set_scores')

@section('menu')
  @include('snippets.main_menu', ['view' => 'admin'])
  @include('snippets.admin_menu', ['view' => 'scores'])
@endsection

@section('view_title', 'Set Scores')

@section('card_content')
<div ng-controller="set_scores" ng-init='initialize(@json($init_options))'>
    <div class="h3 mt-2 mb-5">
      <u>
        @if (!$is_on_done_tab)
          Set New Scores
          <div ng-show="has_available_games()" class="ml-4" style="display: inline-block;">
            @include('snippets.button_modal', [
                'button_action' => 'randomize_all',
                'button_id' => 'randomize_scores',
                'button_label' => 'Randomize Scores',
                'button_class' => 'btn-primary',
                'title' => 'Randomize Scores',
                'msg' => "This will randomize a score for each game shown on table. \n Are you sure?",
                'action_label' => 'Randomize',
                'cancel_label' => 'Cancel'
                ])
          </div>
        @else
          Update Scores
          <div ng-show="has_available_games()" class="ml-4" style="display: inline-block;">
            @include('snippets.button_modal', [
                'button_action' => 'reset_all_scores',
                'button_id' => 'reset_scores',
                'button_label' => 'Reset All Shown Scores',
                'title' => 'Reset Scores',
                'msg' => "This will reset the score of all the games shown on table. \n Are you sure?",
                'action_label' => 'Reset',
                'cancel_label' => 'Cancel'
                ])
          </div>
        @endif
      </u>
    </div>

    <div class="col mt-3">
        @if (!$has_available_games)
          <div class="h5 mb-2">
            @if (!$is_on_done_tab)
              All games are done
            @else
              There are no played games
            @endif
          </div>
        @else
          @include('snippets.table_filters')
          <table ng-cloak class="table table-striped shrunk">
              <thead class="thead-dark">
                  <tr>
                      <th scope="col">Round</th>
                      <th scope="col">Week</th>
                      <th scope="col">Time</th>
                      <th scope="col">Home Team</th>
                      <th scope="col">Away Team</th>
                      <th colspan="3" scope="col">Score</th>
                      <th colspan="2" scope="col">Actions</th>
                  </tr>
              </thead>
              <tbody>
                
                    <tr ng-repeat="game in games">
                      <td class='shrunk'>@{{game.round}}</td>
                      <td class='shrunk'>@{{game.week}}</td>
                      <td class='date_cell'>@{{print_time_cell(game.time)}}</td>
                      <td class="shrunk" ng-class="{'selected_team_cell': game.team_side=='home', 'winner_cell': game.is_home_winner}">@{{game.home_team_name}}</td>
                      <td class="shrunk" ng-class="{'selected_team_cell': game.team_side=='away', 'winner_cell': game.is_away_winner}">@{{game.away_team_name}}</td>

                        <td ng-if-start="game_on_edit == game.id" class='pr-1'>
                          <input type="number" ng-model="home_input[game.id]" min="0" max="20" class="score_input" data-team="home">
                        </td>
                        <td class='shrunk pr-0 pl-0'>:</td>
                        <td ng-if-end class='pl-1'>
                          <input type="number" ng-model="away_input[game.id]" min="0" max="20" class="score_input" data-team="away">
                        </td>

                        <td ng-if-start="is_on_played_tab && game_on_edit !== game.id" class="shrunk pr-1 text-center"
                            ng-class="{'winner_cell': game.is_home_winner}">@{{game.home_score}}</td>
                        <td class='shrunk pr-0 pl-0'>:</td>
                        <td ng-if-end ng-class="{'winner_cell': game.is_away_winner}" class="shrunk pl-0 text-center">@{{game.away_score}}</td>



                        <td ng-if-start="!is_on_played_tab && game_on_edit !== game.id"></td>
                        <td></td>
                        <td ng-if-end></td>


                      <td class='shrunk'>
                        <div ng-show="game_on_edit == game.id" class='confirm_set_score_btn' ng-click="set_score(game.id)"></div>
                        <div ng-hide="game_on_edit == game.id" class='edit_btn' ng-click="edit_game(game)"></div>
                      </td>
                      <td class='shrunk'>
                          <div ng-show="game_on_edit == game.id" class='cancel_set_score_btn' ng-click="cancel_edit()"></div>
                          <div ng-show="game_on_edit !== game.id && is_on_played_tab" class='delete_btn' ng-click="remove_game(game.id)"></div>
                      </td>

                    </tr>
              </tbody>
          </table>
        @endif
    </div>
  </div>
@endsection
