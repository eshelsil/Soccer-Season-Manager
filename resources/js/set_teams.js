function go_to_schedule(){
    window.location = '/schedule';
}

function set_deafult_teams(){ 
    //#NOTE can I be sure that 'params' always will be there because the <script></script> row in set_teams blade>
    let teams = params['teams'];
    $.post('/set_teams', {teams})
    .done(function(){window.location.reload();})
    .fail(function(e){alert(e.responseText)});
}

function empty_teams_table(){
    $.ajax({
        url: '/set_teams/delete_all',
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
    $('#default_teams').click(set_deafult_teams);
    $('#empty_teams_table').click(empty_teams_table);
    $('#to_schedule').click(go_to_schedule);
    $('.delete_team_btn').click(delete_team);
    $('#new_team_add_btn').click(add_team);
})