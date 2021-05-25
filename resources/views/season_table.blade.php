@extends('layouts.app')

@section('title', 'Table')

@section('menu')
    @include('snippets.main_menu', ['view' => 'table'])
@endsection

@php
    $init_options = [ 
        'last_week' => $last_week,
    ];
@endphp

@section('content')
    @if($has_teams)
        <div ng-controller="season_table" ng-init='initialize(@json($init_options));'>
            @if($has_games)
                <div class="col mb-4">
                    @include('snippets.select_input', [
                        'ng_model' => 'until_week_filter',
                        'options_var' => 'until_week_options',
                        'id' => 'weekSelect',
                        'label' => 'Until Week',
                    ])
                </div>
            @endif

            <div class="h3 mt-2 mb-4"><u>
                Season Table
            </u></div>

            <table class="table table-striped shrunk">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Team</th>
                    <th scope="col">Points</th>
                    <th scope="col">Games</th>
                    <th scope="col">W.</th>
                    <th scope="col">D.</th>
                    <th scope="col">L.</th>
                    <th scope="col">GF</th>
                    <th scope="col">GA</th>
                    <th scope="col">GD</th>
                </tr>
            </thead>
            <tbody>
                @foreach($table_rows as $index=>$team)
                    <tr ng-class="{leader_row: {{$team['rank']}} == 1 && {{$team['games']}} > 0}">
                        <td class='shrunk'>{{$team['rank'] == $index + 1 ? $team['rank'] : '-' }}</td>
                        <td class='shrunk'><a href='/games?team={{$team['id']}}'>{{$team['name']}}</a></td>
                        <td class='shrunk'>{{$team['points']}}</td>
                        <td class='shrunk'>{{$team['games']}}</td>
                        <td class='shrunk'>{{$team['wins']}}</td>
                        <td class='shrunk'>{{$team['draws']}}</td>
                        <td class='shrunk'>{{$team['loses']}}</td>
                        <td class='shrunk'>{{$team['goals_for']}}</td>
                        <td class='shrunk'>{{$team['goals_against']}}</td>
                        <td class='shrunk'>{{$team['goals_diff']}}</td>
                    </tr>
                @endforeach
            </tbody>
            </table>
        </div>
    @else
        <div class="h3 mt-2">
            There are no registered teams yet
        </div>
    @endif
@endsection
