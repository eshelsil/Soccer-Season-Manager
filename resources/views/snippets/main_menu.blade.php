<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav" style="width: 100%;">
            <li class="nav-item @if ($view == 'games') active @endif">
                <a class="nav-link" href="/games">Games</a>
            </li>
            <li class="nav-item @if ($view == 'table') active @endif">
                <a class="nav-link" href="/table">Table</a>
            </li>
            <li class="nav-brand nav-item ml-auto @if ($view == 'admin') active  @endif">
                <a class="nav-link navbar-brand" href="/admin">Admin Zone</a>
            </li>
        </ul>
    </div>
</nav>