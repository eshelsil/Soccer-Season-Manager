@extends('layouts.card', [
  'cards' => [
    [
      'url' => '/set_scores/new',
      'label' => 'Non Played Games',
      'active' => $selected_tab == 'unplayed',
    ],
    [
      'url' => '/set_scores/existing',
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
    <div class="h3 mt-2 mb-4"><u>
        @if ($selected_tab == 'unplayed')
          Set New Scores
        @else
          Update Scores
        @endif
    </u></div>
    @csrf

    @if ($selected_tab == 'unplayed' && count($games) > 0)
      <button id="randomize_scores" type="button" class="btn btn-primary">Randomize all non-finished games</button>
    @endif
    <div class="container col mt-3">
        @if (count($games) === 0)
            <div class="h5 mb-2">
              @if ($selected_tab == 'unplayed')
                All games are done
              @else
                There are no played games
              @endif
            </div>
            
        @else
            <div class="h5 mb-2">
              Games:
            </div>
            <table class="table table-striped shrunk">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Round</th>
                        <th scope="col">Week</th>
                        <th scope="col">Home Team</th>
                        <th scope="col">Away Team</th>
                        @if ($selected_tab != 'unplayed')
                        <th colspan="3" scope="col">Score</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($games as $game){
                        $round = $game->round;
                        $week = $game->week;
                        $home_team_name = $teams_by_id[$game->home_team_id];
                        $away_team_name = $teams_by_id[$game->away_team_id];
                        $score_cells = '';
                        $home_winner_class = '';
                        $away_winner_class = '';
                        if ($selected_tab != 'unplayed'){
                          $home_score = $game->home_score;
                          $away_score = $game->away_score;
                          $home_winner_class = ($home_score > $away_score) ? 'font-weight-bold' : '';
                          $away_winner_class = ($home_score < $away_score) ? 'font-weight-bold' : '';
                          $score_cells = "
                          <td class='shrunk pr-0 $home_winner_class'>$home_score</td>
                          <td class='shrunk pr-0 pl-0'>:</td>
                          <td class='shrunk pl-1 $away_winner_class'>$away_score</td>
                          ";
                        }
                        echo "
                        <tr>
                            <td class='shrunk'>$round</td>
                            <td class='shrunk'>$week</td>
                            <td class='shrunk $home_winner_class'>$home_team_name</td>
                            <td class='shrunk $away_winner_class'>$away_team_name</td>
                            $score_cells
                        </tr>
                        ";
                    }
                    ?>
                </tbody>
            </table>
        @endif
    </div>
@endsection