@php
    $default_round_attrs = [
        'id' => 'roundSelect',
        'label' => 'Round',
        'with_all_option' => true,
        'initial_value' => $round_param ?? null,
        'options' => range(1,2)
    ];
    $round_attrs = array_merge($default_round_attrs, $round_attrs ?? []);

    $selected_round = $round_param ?? 'all';
    $selected = $week_param ?? null;
    $weeks_per_round = $weeks_count / 2;
    if ($selected_round != 'all'){
        $available_weeks = range( ( $selected_round - 1 ) * $weeks_per_round + 1 , $selected_round * $weeks_per_round);
    } else {
        $available_weeks = range(1, $weeks_count);
    }

    $default_week_attrs = [
        'id' => 'weekSelect',
        'label' => 'Week',
        'with_all_option' => true,
        'initial_value' => $week_param ?? null,
        'options' => $available_weeks
    ];
    $week_attrs = array_merge($default_week_attrs, $week_attrs ?? []);

    $default_team_attrs = [
        'id' => 'teamSelect',
        'label' => 'Team',
        'with_all_option' => true,
        'initial_value' => $team_id_param ?? null,
        'key_as_value' => true,
        'options' => $teams_by_id
    ];
    $team_attrs = array_merge($default_team_attrs, $team_attrs ?? []);

    $default_filters = ['round', 'week', 'team'];
    $filters = $filters ?? $default_filters;
@endphp
<div class="row mb-3 ml-0 p-0">
    @foreach ($filters as $filter)
        <div class="m-0 pl-0">
            @switch($filter)
                @case('round')
                    @include('snippets.select_input', $round_attrs)
                    @break
                @case('week')
                    @include('snippets.select_input', $week_attrs)
                    @break
                @case('team')
                    @include('snippets.select_input', $team_attrs)
                    @break
            @endswitch
        </div>
    @endforeach
    <div class="mt-auto p-0">
        <button id="reset_filters" type="button" class="btn btn-secondary">Reset Filters</button>
    </div>
</div>
