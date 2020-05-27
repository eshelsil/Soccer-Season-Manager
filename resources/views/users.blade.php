@extends('layouts.app')

@section('title', 'Users')

@section('menu')
@include('snippets.main_menu', ['view' => 'admin'])
@include('snippets.admin_menu', ['view' => 'users'])
@endsection

@section('content')
    <div ng-controller="manage_users" ng-init='initialize()'>
        <div class="h3 mt-2 mb-4"><u>
            <p> Users</p>
        </u></div>

        <table class="table table-striped shrunk">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Username</th>
                    <th scope="col">Role</th>
                    <th colspan="2" scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="user in users">
                    <td class='shrunk'>@{{user.username}}</td>
                    <td class="shrunk text-capitalize">@{{user.role}}</td>
                    <td class="shrunk">
                        <span ng-show="user.role == 'admin'" class="link" ng-click="make_regular(user.id)">Make Regular User</span>
                        <span ng-hide="user.role == 'admin'" class="link" ng-click="make_admin(user.id)">Make Admin User</span>
                    </td>
                    <td class='shrunk'>
                        <span class="link" ng-click="remove_user(user.id, user.id == {{Auth::user()->id}})">Delete</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
