<div class="container row mb-3 ml-0 p-0">
    <div class="container col-2 m-0">
        <label for="roundSelect" class="col pl-0">Round</label>
        <select class="custom-select" id="roundSelect" style="width:auto;">
            <?php
                $selected = $round_param ?? 'all';
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
                $selected_round = $round_param ?? 'all';
                $selected = $week_param ?? 'all';
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
    <div class="container col-5 m-0">
        <label for="teamSelect" class="col pl-0">Team</label>
        <select class="custom-select" id="teamSelect" style="width:auto;">
            <?php
                $selected = $team_id_param ?? 'all';
                echo sprintf("<option value='all' %s>------</option>", $selected == 'all' ? 'selected' : '');
                foreach($teams_by_id as $id => $team_name){
                    $is_selected_str = $selected == $id ? 'selected' : '';
                    echo sprintf("<option value='$id' %s>$team_name</option>", $is_selected_str);
                }
            ?>
        </select>
    </div>
    <div class="container col-3 mt-auto p-0">
        <button id="reset_filters" type="button" class="btn btn-secondary">Reset Filters</button>
    </div>
</div>