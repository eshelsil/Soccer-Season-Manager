function create_games_table(){
    $.post('/manage/init_games')
    .done(function(){window.location.reload();})
    .fail(function(e){alert(e.responseText);});
}

function set_deafult_teams(){ 
    //#NOTE can I be sure that 'params' always will be there because the <script></script> row in set_teams blade>
    let teams = params['teams'];
    $.post('/manage/set_teams', {teams})
    .done(function(){window.location.reload();});
}

function drop_teams_table(){
    $.ajax({
        url: '/manage/drop_teams',
        type: 'DELETE',
        success: function(result) {
            window.location.reload();
        }
    });
}

function delete_team(ev){
    el = $(ev.target);
    team_id = el.data('team_id')
    $.post(`/teams/delete/${team_id}`)
    .done(()=>{window.location.reload()})
    .fail(function(e){alert(e.responseText)});
}

function add_team(){
    team_input = $('#new_team_input')
    team_name = team_input.val()
    console.log({team_name})
    $.post("/teams/add", {name: team_name})
    .done(()=>{window.location.reload()})
    .fail(function(e){alert(e.responseText)});
}



$(document).ready(function(){
    let csrf_tkn = $('input[name="_token"]').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrf_tkn
      }
    });
    $('#default_teams').click(set_deafult_teams);
    $('#drop_teams_table').click(drop_teams_table);
    $('#to_schedule').click(create_games_table);
    $('.delete_team_btn').click(delete_team);
    $('#new_team_add_btn').click(add_team);
})