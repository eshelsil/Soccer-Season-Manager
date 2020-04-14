@extends('layouts.app')

@section('title', 'Games')

@section('script')
    <script src="{{ asset('/js/games.js') }}"></script>
    {{-- #NOTE - is this necessary?  --> I know --}}
@endsection

@section('menu')
    @include('menu', ['view' => 'games'])
@endsection

@section('content')
    @php
        $team_id = $query_params['team_id'];
    @endphp
    <div class="row justify-content-start m-0">
        {{-- #NOTE - should use filters template --}}
        <div class="col-3 m-0">
            <label for="teamSelect" class="row pl-0">Choose Team</label>
            <select class="custom-select row" id="teamSelect" style="width:auto;">
                @php $selected = $query_params['team_id'] ?? 'all'; @endphp
                <option value='all' {{ $selected == 'all' ? 'selected' : '' }}>--- All Teams ---</option>
                @foreach(TEAMS_BY_ID as $id => $team_name)
                    <option value='{{$id}}' {{ $selected == $id ? 'selected' : '' }}>{{$team_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-3 m-0">
            <label for="roundSelect" class="row pl-0">Choose Round</label>
            <select class="custom-select row" id="roundSelect" style="width:auto;">
                @php $selected = $query_params['round'] ?? 'all'; @endphp
                <option value='all' {{ $selected == 'all' ? 'selected' : '' }}>--- All Rounds ---</option>;
                @foreach(range(1,2) as $round)
                    <option {{ $selected == $round ? 'selected' : '' }}>{{$round}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-3 m-0">
            <label for="weekSelect" class="row pl-0">Choose Week</label>
            <select class="custom-select row" id="weekSelect" style="width:auto;">
                @php
                    $selected_round = $query_params['round'] ?? 'all';
                    $selected = $query_params['week'] ?? 'all';
                @endphp
                <option value='all' {{ $selected == 'all' ? 'selected' : '' }}>--- All Weeks ---</option>;
                @foreach(range(1, WEEKS_COUNT) as $week)
                    @if ($selected_round != 'all')
                        @php
                        $available_weeks = range( ( $selected_round - 1 ) * WEEKS_IN_ROUND + 1 , $selected_round * WEEKS_IN_ROUND);
                        @endphp
                        @if (!in_array($week, $available_weeks))
                            @continue
                        @endif
                    @endif
                    <option {{ $selected == $week ? 'selected' : '' }}>{{$week}}</option>
                @endforeach
            </select>
        </div>
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

                if ($score_home > $score_away){
                    $winner_side = 'home';
                } elseif($score_home < $score_away){
                    $winner_side = 'away';
                } elseif (!is_null($score_home) && !is_null($score_away)) {
                    $winner_side = 'draw';
                } else{
                    $winner_side = null;
                }

                if (is_null($team_id)){
                    $team_side = null;
                } elseif($home_team_id == $team_id){
                    $team_side = 'home';
                } else{
                    $team_side = 'away';
                }
                $winner_class = "font-weight-bold";
                $selected_team_class = "underlined";
                $is_home_winner = $winner_side == 'home';
                $is_away_winner = $winner_side == 'away';
                @endphp
                <tr>
                    <td class='shrunk'>{{$week}}</td>
                    @if (!is_null($team_id))
                        @if (is_null($winner_side))
                            <td></td>
                        @elseif($winner_side == 'draw')
                            <td>D</td>
                        @elseif ($winner_side == $team_side)
                            <td class="text-success">W</td>
                        @else
                            <td class="text-danger">L</td>
                        @endif
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