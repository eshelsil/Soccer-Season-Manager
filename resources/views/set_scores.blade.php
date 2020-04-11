@extends('layouts.card', [
  'cards' => [
    [
      'url' => sprintf('/set_scores?%s', http_build_query( array_merge($_GET, ['tab' => 'unplayed']) ) ),
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
@section('menu')
  @include('menu', ['view' => 'set_scores'])
@endsection

@section('view_title', 'Set Scores')

@section('card_content')
    <div class="h3 mt-2 mb-5"><u>
        @if ($selected_tab == 'unplayed')
          Set New Scores
          @if (!$no_available_games)
            <button id="randomize_scores" type="button" class="btn btn-primary ml-5">Randomize all non-finished games</button>
          @endif
        @else
          Update Scores
        @endif
    </u></div>
    @csrf

    <div class="container col mt-3">
        @if ($no_available_games)
            <div class="h5 mb-2">
              @if ($selected_tab == 'unplayed')
                All games are done
              @else
                There are no played games
              @endif
            </div>
            
        @else
            @include('table_filters', [
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
                        @if ($selected_tab != 'unplayed' || ($query_params['set_game_id'] ?? false))
                        <th colspan="3" scope="col">Score</th>
                        @endif
                        <th colspan="2" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($games as $game){
                        $game_id = $game->game_id;
                        $on_edit = ($query_params['set_game_id'] ?? null) == $game_id;
                        $round = $game->round;
                        $week = $game->week;
                        $home_team_id = $game->home_team_id;
                        $away_team_id = $game->away_team_id;
                        $home_team_name = $teams_by_id[$home_team_id];
                        $away_team_name = $teams_by_id[$away_team_id];
                        $score_cells = '';
                        $home_winner_class = '';
                        $away_winner_class = '';

                        $selected_team_id = $query_params['team_id'] ?? null;
                        $underline_home_team = ($selected_team_id == $home_team_id) ? 'underlined' : '';
                        $underline_away_team = ($selected_team_id == $away_team_id) ? 'underlined' : '';


                        $on_edit_action_cells = "
                            <td class='shrunk'>
                                <div class='confirm_set_score_btn' data-game_id=$game_id></div>
                              </td>
                              <td class='shrunk'>
                                <div class='cancel_set_score_btn'></div>
                              </td>
                            </tr>
                            ";
                        $on_edit_score_cells_tmpl = "
                            <td class='pr-1'>
                              home_input
                            </td>
                            <td class='shrunk pr-0 pl-0'>:</td>
                            <td class='pl-1'>
                              away_input
                            </td>
                            ";

                        if ($selected_tab != 'unplayed'){
                          $home_score = $game->home_score;
                          $away_score = $game->away_score;
                          if (!$on_edit){
                            $home_winner_class = ($home_score > $away_score) ? 'font-weight-bold' : '';
                            $away_winner_class = ($home_score < $away_score) ? 'font-weight-bold' : '';
                            $score_cells = "
                            <td class='shrunk pr-1 text-center $home_winner_class'>$home_score</td>
                            <td class='shrunk pr-0 pl-0'>:</td>
                            <td class='shrunk pl-0 text-center $away_winner_class'>$away_score</td>
                            ";
                              $action_cells = "
                            <td class='shrunk'>
                                <div class='edit_btn' data-game_id=$game_id></div>
                              </td>
                              <td class='shrunk'>
                                <div class='delete_btn' data-game_id=$game_id></div>
                              </td>
                            </tr>
                            ";
                          } else {
                            $input_tmpl = '<input type="number" value="score_value" min="0" max="20" class="score_input" data-team="team_side">';
                            $home_input = strtr($input_tmpl, ['score_value' => $home_score, 'team_side' => 'home']);
                            $away_input = strtr($input_tmpl, ['score_value' => $away_score, 'team_side' => 'away']);
                            $score_cells = strtr($on_edit_score_cells_tmpl, ['home_input' => $home_input, 'away_input' => $away_input]);
                            $action_cells = $on_edit_action_cells;
                          }
                        }else{
                          if ($on_edit) {
                            $input_tmpl = '<input type="number" value="0" min="0" max="20" class="score_input" data-team="team_side">';
                            $home_input = strtr($input_tmpl, ['team_side' => 'home']);
                            $away_input = strtr($input_tmpl, ['team_side' => 'away']);
                            $score_cells = strtr($on_edit_score_cells_tmpl, ['home_input' => $home_input, 'away_input' => $away_input]);
                            $action_cells = $on_edit_action_cells;
                          } else {
                            if ($query_params['set_game_id'] ?? false){
                              $score_cells="<td></td><td></td><td></td>";
                            }
                            $action_cells = "
                            <td class='shrunk'>
                                <div class='edit_btn' data-game_id=$game_id></div>
                              </td>
                              <td class='shrunk'>
                              </td>
                            </tr>
                            ";
                          }
                        }
                        echo "
                        <tr>
                            <td class='shrunk'>$round</td>
                            <td class='shrunk'>$week</td>
                            <td class='shrunk $home_winner_class $underline_home_team'>$home_team_name</td>
                            <td class='shrunk $away_winner_class $underline_away_team'>$away_team_name</td>
                            $score_cells
                            $action_cells
                        </tr>
                        ";
                    }
                    ?>
                </tbody>
            </table>
        @endif
    </div>
@endsection