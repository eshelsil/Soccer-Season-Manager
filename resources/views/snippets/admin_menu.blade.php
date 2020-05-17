@php
    $disabled_views = $disabled_views ?? [];
@endphp

<nav ng-controller="admin_menu" class="mt-2 navbar navbar-expand-lg navbar-dark" style="background-color:#11348e;">
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item @if ($view == 'teams') active @endif">
                <a class="nav-link" ng-class="{disabled: is_view_disabled('teams')}" href="/admin/teams">Register Teams</a>
            </li>
            <li class="nav-item @if ($view == 'schedule') active @endif">
                <a class="nav-link" ng-class="{disabled: is_view_disabled('schedule')}" href="/admin/schedule">Schedule Games</a>
            </li>
            <li class="nav-item @if ($view == 'scores') active @endif">
                <a class="nav-link" ng-class="{disabled: is_view_disabled('scores')}" href="/admin/scores">Set Scores</a>
            </li>
        </ul>
    </div>
</nav>