@extends('layouts.app')

@section('title', 'Set Teams')
@section('script')
    <script>
        var params = @json(['teams'=>Config::get('constants.DEAFULT_TEAMS_LIST')])
    </script>
@endsection

@section('content')
    @csrf
    <div class="h3 mt-2 mb-4"><u>
        Step 1 - Set Teams
    </u></div>

    <div class="container row">
        
        <div class="container row p-4">
            <input type="text" maxlength="50" id="new_team_input">
            <button id="new_team_add_btn" type="button" class="btn btn-primary">Add Team</button>
        </div>

        <div class="container row p-4">
            <div class="col-6 p-2 bg-white border border-dark rounded">
                <div class="container col">
                    <div class="h5 mb-1">Teams:</div>
                    <?php
                        foreach($teams_by_id as $id => $name){
                            
                            echo "
                                <div class='container row p-1'>
                                    <div class='delete_team_btn' data-team_id=$id></div>
                                    <p class='mb-0'>$name</p>
                                </div>
                            ";
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <button id="drop_teams_table" type="button" class="btn btn-danger">Drop teams table</button>
    <button id="default_teams" type="button" class="btn btn-primary">Use default temas</button>
    <button id="to_schedule" type="button" class="btn btn-success">Continue to schedule games</button>
    
@endsection