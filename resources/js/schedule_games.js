function auto_schedule(){
    $.post('/schedule/auto')
    .done(()=>{window.location.reload()})
    .fail(function(e){alert(e.responseText)});
}

function schedule_game(){
    week = $('#setWeekSelect').val();
    home_team_id = $('#homeTeamSelect').val();
    away_team_id = $('#awayTeamSelect').val();
    $.post('/schedule/add_game', {
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
    $.post(`/schedule/delete_game/${game_id}`)
    .done(()=>{window.location.reload()})
    .fail(function(e){alert(e.responseText)});
}
function go_to_set_scores(){
    window.location = '/set_scores';
}

function truncate_games_table(){
    //#NOTE this method exist in reset.js
    
    $.post('/schedule/reset_games')
    .done(()=>{
        window.location.reload();
    })
    .fail(function(e){alert(e.responseText)});
}

function go_to_set_teams(){
    window.location = '/set_teams';
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
    $('#go_to_set_teams').click(go_to_set_teams);
    $('#reset_filters').click(reset_filters);
    $('#to_set_score').click(go_to_set_scores);
    $('#schedule_game_button').click(schedule_game);
    $('.delete_game_btn').click(delete_game);
})