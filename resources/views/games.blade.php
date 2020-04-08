@extends('layouts.app')

@section('title', 'Games')

@section('script')
    <script src="{{ asset('/js/games.js') }}"></script>
@endsection

@section('menu')
    @include('menu', ['view' => 'games'])
@endsection

@section('content')
    <div class="container row justify-content-start">
        <div class="container col-3 m-0">
            <label for="teamSelect" class="row pl-0">Choose Team</label>
            <select class="custom-select row" id="teamSelect" style="width:auto;">
                <?php
                    $selected = $query_params['team_id'] ?? 'all';
                    echo sprintf("<option value='all' %s>--- All Teams ---</option>", $selected == 'all' ? 'selected' : '');
                    foreach(TEAMS_BY_ID as $id => $team_name){
                        $is_selected_str = $selected == $id ? 'selected' : '';
                        echo sprintf("<option value='$id' %s>$team_name</option>", $is_selected_str);
                    }
                ?>
            </select>
        </div>
        <div class="container col-3 m-0">
            <label for="roundSelect" class="row pl-0">Choose Round</label>
            <select class="custom-select row" id="roundSelect" style="width:auto;">
                <?php
                    $selected = $query_params['round'] ?? 'all';
                    echo sprintf("<option value='all' %s>--- All Rounds ---</option>", $selected == 'all' ? 'selected' : '');
                    foreach(range(1,2) as $round){
                        $is_selected_str = $selected == $round ? 'selected' : '';
                        echo sprintf("<option %s>$round</option>", $is_selected_str);
                    }
                ?>
            </select>
        </div>
        <div class="container col-3 m-0">
            <label for="weekSelect" class="row pl-0">Choose Week</label>
            <select class="custom-select row" id="weekSelect" style="width:auto;">
                <?php
                    $selected_round = $query_params['round'] ?? 'all';
                    $selected = $query_params['week'] ?? 'all';
                    echo sprintf("<option value='all' %s>--- All Weeks ---</option>", $selected == 'all' ? 'selected' : '');
                    foreach(range(1, WEEKS_COUNT) as $week){
                        if ($selected_round != 'all'){
                            $available_weeks = range( ( $selected_round - 1 ) * WEEKS_IN_ROUND + 1 , $selected_round * WEEKS_IN_ROUND);
                            if (!in_array($week, $available_weeks)){
                                continue;
                            }
                        }
                        $is_selected_str = $selected == $week ? 'selected' : '';
                        echo sprintf("<option %s>$week</option>", $is_selected_str);
                    }
                ?>
            </select>
        </div>
    </div>
        
        <div class="h3 mt-2 mb-4"><u>
            <?php
                $team_id = $query_params['team_id'];
                if (!is_null($team_id)){
                    $team_name = TEAMS_BY_ID[$team_id];
                    echo "Games of $team_name";
                } else {
                    echo "Games";
                }
                ?>
        </u></div>
    
    
    <table class="table table-striped shrunk">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Week</th>
                <th scope="col">Home Team</th>
                <th scope="col">Away Team</th>
                <th colspan="3" scope="col">Score</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $team_id = $query_params['team_id'];
            foreach($games as $game){
                $round = $game->round;
                $week = $game->week;
                $home_team_id = $game->home_group_id;
                $away_team_id = $game->away_group_id;
                $home_team_name = TEAMS_BY_ID[$home_team_id];
                $away_team_name = TEAMS_BY_ID[$away_team_id];
                $score_home = $game->home_score;
                $score_away = $game->away_score;
                $home_winner_class = '';
                $away_winner_class = '';
                $home_team_class = '';
                $away_team_class = '';
                $score_separator = ':';
                if ($score_home > $score_away){
                    $home_winner_class = 'font-weight-bold';
                } elseif ($score_home < $score_away) {
                    $away_winner_class = 'font-weight-bold';
                }
                $is_done = $game->is_done;
                if (!$is_done){
                    $score_separator = '';
                }
                $home_team_text = ($home_team_id == $team_id) ? "<u>$home_team_name</u>" : $home_team_name;
                $away_team_text = ($away_team_id == $team_id) ? "<u>$away_team_name</u>" : $away_team_name;
                echo "
                <tr>
                    <td class='shrunk'>$week</td>
                    <td class='shrunk $home_winner_class'>$home_team_text</td>
                    <td class='shrunk $away_winner_class'>$away_team_text</td>
                    <td class='shrunk pr-0 $home_winner_class'>$score_home</td>
                    <td class='shrunk pr-0 pl-0'>$score_separator</td>
                    <td class='shrunk pl-1 $away_winner_class'>$score_away</td>
                </tr>
                ";
            }
            ?>
        </tbody>
    </table>
@endsection