<div class="row mb-3 ml-0 p-0">
    <div class="m-0 pl-0">
        @include('snippets.select_input', [
            'id' => 'roundSelect',
            'label' => 'Round',
            'with_all_option' => true,
            'initial_value' => $round_param ?? null,
            'options' => range(1,2)
        ])
    </div>
    <div class="m-0 pl-0">
        @php
            $selected_round = $round_param ?? 'all';
            $selected = $week_param ?? null;
            $weeks_per_round = $weeks_count / 2;
            if ($selected_round != 'all'){
                $available_weeks = range( ( $selected_round - 1 ) * $weeks_per_round + 1 , $selected_round * $weeks_per_round);
            } else {
                $available_weeks = range(1, $weeks_count);
            }
        @endphp
        @include('snippets.select_input', [
            'id' => 'weekSelect',
            'label' => 'Week',
            'with_all_option' => true,
            'initial_value' => $week_param ?? null,
            'options' => $available_weeks
        ])
    </div>
    <div class="m-0 pl-0">
        @include('snippets.select_input', [
            'id' => 'teamSelect',
            'label' => 'Team',
            'with_all_option' => true,
            'initial_value' => $team_id_param ?? null,
            'key_as_value' => true,
            'options' => $teams_by_id
        ])
    </div>
    <div class="col-3 mt-auto p-0">
        <button id="reset_filters" type="button" class="btn btn-secondary">Reset Filters</button>
    </div>
</div>
