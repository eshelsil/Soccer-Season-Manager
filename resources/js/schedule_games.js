function auto_schedule(){
    $.post('/manage/auto_schedule')
    .done(()=>{window.location.reload()})
    .fail(function(e){alert(e.responseText)});
}

function schedule_game(){
    week = $('#setWeekSelect').val();
    home_team_id = $('#homeTeamSelect').val();
    away_team_id = $('#awayTeamSelect').val();
    $.post('/manage/schedule_game', {
        week,
        home_team_id,
        away_team_id
    })
    .done(()=>{window.location.reload()})
    .fail(function(e){alert(e.responseText)});
}
function delete_game(ev){
    el = $(ev.target);
    game_id = el.data('game_id')
    $.post(`/manage/delete_game/${game_id}`)
    .done(()=>{window.location.reload()})
    .fail(function(e){alert(e.responseText)});
}
function go_to_set_scores(){
    window.location = '/set_scores';
}

function truncate_games_table(){
    //#NOTE this method exist in reset.js
    
    $.post('/manage/reset_games')
    .done(()=>{
        window.location = '/manage';
    })
    .fail(function(e){alert(e.responseText)});
}

function drop_games_table(){
    $.ajax({
        url: '/manage/drop_games',
        type: 'DELETE',
        success: function(result) {
            window.location.reload();
        }
    });
}

function reset_filters(){
    url = new URL(window.location);
    url.searchParams.delete('team_id');
    url.searchParams.delete('week');
    url.searchParams.delete('round');
    window.location = url.href;
}



$(document).ready(function(){
    $('#auto_schedule').click(auto_schedule);
    $('#truncate_games_table').click(truncate_games_table);
    $('#drop_games_table').click(drop_games_table);
    $('#reset_filters').click(reset_filters);
    $('#to_set_score').click(go_to_set_scores);
    $('#schedule_game_button').click(schedule_game);
    $('.delete_game_btn').click(delete_game);
})