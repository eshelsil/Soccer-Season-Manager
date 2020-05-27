@php
    $is_admin = Auth::user()->isAdmin();
@endphp
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav" style="width: 100%;">
            @if ($is_admin)
            <li class="nav-brand nav-item @if ($view == 'admin') active  @endif">
                <a class="nav-link navbar-brand" href="/admin">Admin Zone</a>
            </li>
            @endif
            <li class="nav-item @if ($view == 'games') active @endif">
                <a class="nav-link" href="/games">Games</a>
            </li>
            <li class="nav-item @if ($view == 'table') active @endif">
                <a class="nav-link" href="/table">Table</a>
            </li>

            <li class="nav-item dropdown ml-auto" style="display: flex;">
                <div class="user_icon m-auto"></div>
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    {{ Auth::user()->username }} <span class="caret"></span>
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>
                    <a class="dropdown-item" href="" onclick="event.preventDefault();" ng-click="delete_active_user()">
                        {{ __('Delete User (and logout)') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>