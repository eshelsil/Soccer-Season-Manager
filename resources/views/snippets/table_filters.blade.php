@php
    $default_filters = ['round', 'week', 'team'];
    $filters = $filters ?? $default_filters;

    if(in_array('round', $filters)){
        $default_round_attrs = [
            'ng_model' => 'round_filter',
            'options_var' => 'round_filter_options',
            'id' => 'roundSelect',
            'label' => 'Round'
        ];
        $round_attrs = array_merge($default_round_attrs, $round_attrs ?? []);
    }

    if(in_array('week', $filters)){
        $default_week_attrs = [
            'ng_model' => 'week_filter',
            'options_var' => 'week_filter_options',
            'id' => 'weekSelect',
            'label' => 'Week'
        ];
        $week_attrs = array_merge($default_week_attrs, $week_attrs ?? []);
    }

    if(in_array('team', $filters)){
        $default_team_attrs = [
            'ng_model' => 'team_filter',
            'options_var' => 'team_filter_options',
            'id' => 'teamSelect',
            'label' => 'Team'
        ];
        $team_attrs = array_merge($default_team_attrs, $team_attrs ?? []);
    }

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
        <button type="button" class="btn btn-secondary" ng-click="reset_table_filters()">Reset Filters</button>
    </div>
</div>
