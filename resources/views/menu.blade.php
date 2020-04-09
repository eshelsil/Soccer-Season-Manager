<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    {{-- <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button> --}}
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item @if ($view == 'games') active @endif">
                <a class="nav-link" href="/games">Games</a>
            </li>
            <li class="nav-item @if ($view == 'table') active @endif">
                <a class="nav-link" href="/table">Table</a>
            </li>
            <li class="nav-item @if ($view == 'set_scores') active @endif">
                <a class="nav-link" href="/set_scores">Set Scores</a>
            </li>
            <li class="nav-item @if ($view == 'reset_options') active @endif">
                <a class="nav-link" href="/reset_options">Reset Options</a>
            </li>
        </ul>
    </div>
</nav>