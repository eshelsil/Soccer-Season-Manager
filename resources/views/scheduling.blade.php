@extends('layouts.app')

@section('title', 'Scheduling')

@section('content')
    <div class="h3 mt-2 mb-4"><u>
        Step 2 - Schedule Games
    </u></div>
    @csrf

    <div class="container row">

        <div class="container col-5 mt-3 pl-0">
            @if (empty($weeks_to_schedule))
                <div class="h4 mb-2">No more games to schedule :)</div>
            @else
                <div class="container row-1 mb-3 p-0">
                    <button id="auto_schedule" type="button" class="btn btn-primary">Auto schedule all games</button>
                </div>
                <div class="h4 mb-2"><u>Schedule a game:</u></div>
                <div class="p-3 border border-dark rounded" style="background: #dcf0ff;">
                    <div class="container col p-0">
                        <div class="container row mb-3  ml-0 mr-0 p-0">
                            <div class="container col p-0">
                                <label class="row m-0">Round</label>
                                <?php
                                    $teams_count = count(array_keys($teams_by_id));
                                    $games_per_round = $teams_count - 1;
                                    $round = ceil($query_params['set_week'] / $games_per_round);
                                    echo sprintf('<input type="text" id ="round_input" maxlength="1" value="%s" disabled style="width:1rem;">', $round);
                                ?>
                            </div>
                            <div class="container col m-0 p-0">
                                <label for="setWeekSelect" class="row m-0">Week</label>
                                <select class="custom-select" id="setWeekSelect" style="width:auto;">
                                    <?php
                                        foreach($weeks_to_schedule as $week){
                                            echo sprintf("<option %s>$week</option>", ($query_params['set_week'] == $week) ? 'selected' : '');
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="container row m-0 p-0">
                            <div class="container col m-0 p-0">
                                <label for="homeTeamSelect" class="col m-0 p-0">Home Team</label>
                                <select class="custom-select" id="homeTeamSelect" style="width:auto;">
                                    <?php
                                        foreach($available_teams as $team_id){
                                            $team_name = $teams_by_id[$team_id];
                                            echo "<option value='$team_id'>$team_name</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="container col m-0 p-0">
                                <label for="awayTeamSelect" class="col m-0 p-0">Away Team</label>
                                <select class="custom-select" id="awayTeamSelect" style="width:auto;">
                                    <?php
                                        foreach($available_teams as $team_id){
                                            $team_name = $teams_by_id[$team_id];
                                            $is_selected = array_values($available_teams)[1] == $team_id;
                                            echo sprintf("<option value='$team_id' %s>$team_name</option>", ($is_selected) ? 'selected' : '');
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="container row justify-content-center mt-4">
                        <button id="schedule_game_button" type="button" class="btn btn-primary">Add Game</button>
                    </div>
                </div>
            @endif
        </div>

        <div class="container col-7 mt-3 pl-2">
            <div class="container row-1 mb-3 p-0">
                @if (count($games) === 0)
                    <button id="drop_games_table" type="button" class="btn btn-secondary mr-2">Back to set teams</button>
                @else
                    <button id="truncate_games_table" type="button" class="btn btn-danger mr-2">Delete all games</button>
                @endif
                @if ($allow_set_scores)
                    <button id="to_set_score" type="button" class="btn btn-success">Continue to set scores</button>
                @endif
            </div>
            <div class="h4 mb-2"><u>Scheduled Games:</u></div>
            <div class="p-2 border border-dark rounded" style="background: #dcf0ff;">
                @if (count($games) === 0)
                    <div class="h5 mb-2">There are no scheduled games yet</div>
                @else
                    <div class="container row mb-3">
                        <div class="container col-2 m-0">
                            <label for="roundSelect" class="col pl-0">Round</label>
                            <select class="custom-select" id="roundSelect" style="width:auto;">
                                <?php
                                    $selected = $query_params['round'] ?? 'all';
                                    echo sprintf("<option value='all' %s>---</option>", $selected == 'all' ? 'selected' : '');
                                    foreach(range(1,2) as $round){
                                        $is_selected_str = $selected == $round ? 'selected' : '';
                                        echo sprintf("<option %s>$round</option>", $is_selected_str);
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="container col-2 m-0">
                            <label for="weekSelect" class="col pl-0">Week</label>
                            <select class="custom-select" id="weekSelect" style="width:auto;">
                                <?php
                                    $selected_round = $query_params['round'] ?? 'all';
                                    $selected = $query_params['week'] ?? 'all';
                                    echo sprintf("<option value='all' %s>---</option>", $selected == 'all' ? 'selected' : '');
                                    $weeks_per_round = $weeks_count / 2;
                                    foreach(range(1, $weeks_count) as $week){
                                        if ($selected_round != 'all'){
                                            $available_weeks = range( ( $selected_round - 1 ) * $weeks_per_round + 1 , $selected_round * $weeks_per_round);
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
                        <div class="container col-6 m-0">
                            <label for="teamSelect" class="col pl-0">Team</label>
                            <select class="custom-select" id="teamSelect" style="width:auto;">
                                <?php
                                    $selected = $query_params['team_id'] ?? 'all';
                                    echo sprintf("<option value='all' %s>------</option>", $selected == 'all' ? 'selected' : '');
                                    foreach($teams_by_id as $id => $team_name){
                                        $is_selected_str = $selected == $id ? 'selected' : '';
                                        echo sprintf("<option value='$id' %s>$team_name</option>", $is_selected_str);
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
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
                            <?php
                            foreach($filtered_games as $game){
                                $game_id = $game->game_id;
                                $round = $game->round;
                                $week = $game->week;
                                $home_team_name = $teams_by_id[$game->home_team_id];
                                $away_team_name = $teams_by_id[$game->away_team_id];
                                echo "
                                <tr>
                                    <td class='shrunk'>$round</td>
                                    <td class='shrunk'>$week</td>
                                    <td class='shrunk'>$home_team_name</td>
                                    <td class='shrunk'>$away_team_name</td>
                                    <td class='shrunk'>
                                        <div class='delete_game_btn' data-game_id=$game_id></div>
                                    </td>
                                </tr>
                                ";
                            }
                            ?>
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection