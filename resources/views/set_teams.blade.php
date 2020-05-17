@extends('layouts.app')

@section('title', 'Set Teams')
@php
    $init_options = ['default_teams' => Config::get('default_inputs.TEAMS_LIST')]
@endphp

@section('menu')
  @include('snippets.main_menu', ['view' => 'admin'])
  @include('snippets.admin_menu', ['view' => 'teams'])
@endsection

@section('content')
    <div ng-controller="teams_registration" ng-init='initialize(@json($init_options));'>

        <div class="h3 mt-2 mb-4"><u>
            Set Teams
        </u></div>

        <div class="col p-0">
            
            <form name="add_team_from" class="row p-4">
                <input type="text" maxlength="50" ng-model="new_team_input" name="team_name" required >
                <button ng-click="add_team()" type="button" class="btn btn-primary">Add Team</button>
            </form>

            <div class="row p-4">
                <div class="col-6 p-2 bg-white border border-dark rounded">
                    <div class="col">
                        <div class="h5 mb-1">Teams:</div>
                        <div ng-repeat="team in teams" class='row p-1'>
                            <div class='delete_team_btn' ng-click="remove_team(team.id)"></div>
                            <p class='mb-0'>@{{team.name}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button ng-click="remove_all_teams()" type="button" class="btn btn-danger">Delete all teams</button>
        <button ng-click="use_deafult_teams()" type="button" class="btn btn-primary">Use default teams</button>
        <button ng-class="{'disabled_button': !can_start_scheduling()}" ng-click="go_to_schedule()"
                type="button" class="btn btn-success">Continue to schedule games</button>
    </div>
    
@endsection