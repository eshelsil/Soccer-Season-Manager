function create_games_table(){
    $.post('/manage/init_games')
    .done(function(){window.location.reload();})
    .fail(function(e){alert(e.responseText);});
}

function set_deafult_teams(){ 
    let teams = params['teams'];
    $.post('/manage/add_teams', {teams})
    .done(function(){window.location.reload();});
}

function drop_teams_table(){
    $.ajax({
        url: '/manage/drop_teams',
        type: 'DELETE',
        success: function(result) {
            alert('cool');
            window.location.reload();
        }
    });
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
})