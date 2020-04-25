{{-- @php
  $str_query = "blablabla"
@endphp --}}
@extends('layouts.card', [
  'cards' => [
    [
      #NOTE what if I want to pass more complicated php (not one line) as a parameter?
      'url' => '/set_scores?'. http_build_query( array_merge($_GET, ['tab' => 'unplayed']) ),
      // 'url' => sprintf('/set_scores?%s', http_build_query( array_merge($_GET, ['tab' => 'unplayed']) ) ),
      'label' => 'Non Played Games',
      'active' => $selected_tab == 'unplayed',
    ],
    [
      'url' => sprintf('/set_scores?%s', http_build_query( array_merge($_GET, ['tab' => 'played']) ) ),
      'label' => 'Played Games',
      'active' => $selected_tab == 'played',
    ]
  ]
])

@section('title', 'Set Scores')
@section('container', 'set_scores')

{{-- @section('script')
    <script src="{{ asset('/js/set_scores.js') }}"></script>
@endsection --}}

@section('menu')
  @include('snippets.menu', ['view' => 'set_scores'])
@endsection

@section('view_title', 'Set Scores')

@section('card_content')
<div ng-controller="set_scores">
    <div class="h3 mt-2 mb-5"><u>
        @if ($selected_tab == 'unplayed')
          Set New Scores
          @if ($has_available_games)
            <button id="randomize_scores" type="button" class="btn btn-primary ml-5">Randomize all non-finished games</button>
          @endif
        @else
          Update Scores
        @endif
    </u></div>

    <div class="col mt-3">
        @if (!$has_available_games)
            <div class="h5 mb-2">
              @if ($selected_tab == 'unplayed')
                All games are done
              @else
                There are no played games
              @endif
            </div>

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
                        {{-- @if ($selected_tab != 'unplayed' || ($query_params['set_game_id'] ?? false)) --}}
                        <th colspan="3" scope="col" ng-show="game_on_edit !== undefined">Score</th>
                        {{-- <th colspan="3" scope="col">Score</th> --}}
                        {{-- @endif --}}
                        <th colspan="2" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach ($games as $game)
                      @php
                        $game_id = $game->game_id;
                        $edit_is_on = !is_null($query_params['set_game_id']);
                        $row_on_edit = ($query_params['set_game_id'] ?? null) == $game_id;
                        $round = $game->round;
                        $week = $game->week;
                        $home_team_id = $game->home_team_id;
                        $away_team_id = $game->away_team_id;
                        $home_team_name = $teams_by_id[$home_team_id];
                        $away_team_name = $teams_by_id[$away_team_id];
                        $home_score = $game->home_score;
                        $away_score = $game->away_score;

                        $is_home_winner = $game->isHomeWinner();
                        $is_away_winner = $game->isAwayWinner();
                        $selected_team_id = $query_params['team_id'] ?? null;
                        $team_side = $game->getTeamSide($selected_team_id);
                        $selected_team_class = 'underlined';
                        $winner_class = 'font-weight-bold';
                        $played_tab_is_on = $selected_tab == 'played';
                      @endphp
                      <tr>
                        <td class='shrunk'>{{$round}}</td>
                        <td class='shrunk'>{{$week}}</td>
                        <td class='shrunk {{$is_home_winner ? $winner_class : ''}} {{$team_side == 'home' ? $selected_team_class : ''}}'>{{$home_team_name}}</td>
                        <td class='shrunk {{$is_away_winner ? $winner_class : ''}} {{$team_side == 'away' ? $selected_team_class : ''}}'>{{$away_team_name}}</td>

                          <td ng-if-start="game_on_edit == {{$game_id}}" class='pr-1'>
                            <input type="number" value="{{$played_tab_is_on ? $home_score : 0}}" min="0" max="20" class="score_input" data-team="home">
                          </td>
                          <td class='shrunk pr-0 pl-0'>:</td>
                          <td ng-if-end class='pl-1'>
                            <input type="number" value="{{$played_tab_is_on ? $away_score : 0}}" min="0" max="20" class="score_input" data-team="away">
                          </td>

                          <td ng-if-start="selected_tab == played && game_on_edit !== {{$game_id}}" class='shrunk pr-1 text-center {{$is_home_winner ? $winner_class : ''}}'>{{$home_score}}</td>
                          <td class='shrunk pr-0 pl-0'>:</td>
                          <td ng-if-end class='shrunk pl-0 text-center {{$is_away_winner ? $winner_class : ''}}'>{{$away_score}}</td>



                          <td ng-if-start="game_on_edit !== undefined && selected_tab !== played && game_on_edit !== {{$game_id}}"></td>
                          <td></td>
                          <td ng-if-end></td>

                        {{-- @if ($row_on_edit)
                          <td class='pr-1'>
                            <input type="number" value="{{$played_tab_is_on ? $home_score : 0}}" min="0" max="20" class="score_input" data-team="home">
                          </td>
                          <td class='shrunk pr-0 pl-0'>:</td>
                          <td class='pl-1'>
                            <input type="number" value="{{$played_tab_is_on ? $away_score : 0}}" min="0" max="20" class="score_input" data-team="away">
                          </td>
                        @elseif ($played_tab_is_on)
                          <td class='shrunk pr-1 text-center {{$is_home_winner ? $winner_class : ''}}'>{{$home_score}}</td>
                          <td class='shrunk pr-0 pl-0'>:</td>
                          <td class='shrunk pl-0 text-center {{$is_away_winner ? $winner_class : ''}}'>{{$away_score}}</td>
                        @elseif ($edit_is_on)
                          <td></td>
                          <td></td>
                          <td></td>
                        @endif

                        <td class='shrunk'>
                          @if ($row_on_edit)
                            <div class='confirm_set_score_btn' data-game_id={{$game_id}}></div>
                          @else
                            <div class='edit_btn' ng-click="edit_game({{$game_id}})"></div>
                          @endif
                        </td>
                        <td class='shrunk'>
                          @if ($row_on_edit)
                            <div class='cancel_set_score_btn'></div>
                          @elseif ($played_tab_is_on)
                        <div class='delete_btn' ng-click="remove_game({{$game_id}})"></div>
                          @endif
                        </td>
                      </tr>
                  @endforeach
                </tbody>
            </table>
        @endif
    </div>
  </div>
@endsection
