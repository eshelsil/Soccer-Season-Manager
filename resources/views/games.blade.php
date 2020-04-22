@extends('layouts.app')

@section('title', 'Games')

@section('script')
    <script src="{{ asset('/js/games.js') }}"></script>
    {{-- #NOTE - is this necessary?  --> I know --}}
@endsection

@section('menu')
    @include('snippets.menu', ['view' => 'games'])
@endsection

@section('content')
    @php
        $team_id = $query_params['team_id'];
    @endphp
    <div class="row justify-content-start m-0">
        @include('snippets.table_filters', [
            'filters' => ['team', 'round', 'week'],
            'round_param' => $query_params['round'],
            'week_param' => $query_params['week'],
            'team_id_param' => $query_params['team_id'],
            'weeks_count' => WEEKS_COUNT,
            'teams_by_id' => TEAMS_BY_ID,
            'team_attrs' => [
                'label' => 'Choose Team'
            ],
            'round_attrs' => [
                'label' => 'Choose Round'
            ],
            'week_attrs' => [
                'label' => 'Choose Week'
            ]
        ])
    </div>

        <div class="h3 mt-2 mb-4"><u>
            @if (!is_null($team_id))
                Games of {{TEAMS_BY_ID[$team_id]}}
            @else
                Games
            @endif
        </u></div>


    <table class="table table-striped shrunk">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Week</th>
                @if (!is_null($team_id))
                <th scope="col">Res.</th>
                @endif
                <th scope="col">Home Team</th>
                <th scope="col">Away Team</th>
                <th colspan="3" scope="col">Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($games as $game)
                @php
                $round = $game->round;
                $week = $game->week;
                $home_team_id = $game->home_team_id;
                $away_team_id = $game->away_team_id;
                $home_team_name = TEAMS_BY_ID[$home_team_id];
                $away_team_name = TEAMS_BY_ID[$away_team_id];
                $score_home = $game->home_score;
                $score_away = $game->away_score;
                $is_done = $game->is_done;

                $team_side = $game->getTeamSide($team_id);
                $team_result = $game->getTeamResult($team_id);
                $is_home_winner = $game->isHomeWinner();
                $is_away_winner = $game->isAwayWinner();

                $winner_class = "font-weight-bold";
                $selected_team_class = "underlined";
                @endphp
                <tr>
                    <td class='shrunk'>{{$week}}</td>
                    @if (!is_null($team_id))
                        @switch ($team_result)
                            @case('draw')
                                <td>D</td>
                                @break
                            @case('win')
                                <td class="text-success">W</td>
                                @break
                            @case('lose')
                                <td class="text-danger">L</td>
                                @break
                            @default
                                <td></td>
                        @endswitch
                    @endif
                    <td class='shrunk {{$is_home_winner ? $winner_class : ''}} {{$team_side == 'home' ? $selected_team_class : ''}}'>{{$home_team_name}}</td>
                    <td class='shrunk {{$is_away_winner ? $winner_class : ''}} {{$team_side == 'away' ? $selected_team_class : ''}}'>{{$away_team_name}}</td>
                    <td class='shrunk pr-0 {{$is_home_winner ? $winner_class : ''}}'>{{$score_home}}</td>
                    <td class='shrunk pr-0 pl-0'>{{$is_done ? ':' : ''}}</td>
                    <td class='shrunk pl-1 {{$is_away_winner ? $winner_class : ''}}'>{{$score_away}}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
@endsection
