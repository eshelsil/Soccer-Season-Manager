@extends('layouts.app')

@section('title', 'Set Teams')
@section('script')
    <script>
        var params = @json(['teams'=>Config::get('constants.DEAFULT_TEAMS_LIST')])
    </script>
@endsection

@section('content')
    <div class="h3 mt-2 mb-4"><u>
        Step 1 - Set Teams
    </u></div>
    <div class="container col">
        <div class="h5 mb-1">Teams:</div>
        <?php
            foreach($teams_by_id as $id => $name){
                echo "<p class='mb-0'>$name</p>";
            }
        ?>
    </div>
    @csrf
    <button id="drop_teams_table" type="button" class="btn btn-danger">drop teams table</button>
    <button id="default_teams" type="button" class="btn btn-primary">use default temas</button>
    <button id="to_schedule" type="button" class="btn btn-success">continue to schedule games</button>
@endsection