function randomize_scores(){
    $.post('/set_scores/randomize')
    .done(()=>{window.location.reload()})
    .fail(function(e){alert(e.responseText)});
}

function reset_game_score(ev){
    el = $(ev.target);
    game_id = el.data('game_id')
    $.post(`/set_scores/delete/${game_id}`)
    .done(()=>{window.location.reload()})
    .fail(function(e){alert(e.responseText)});
}

function cancel_set_score(ev){
    url = new URL(window.location);
    url.searchParams.delete('set_game_id');
    window.location = url.href;
}

function go_to_set_game_score(ev){
    el = $(ev.target);
    url = new URL(window.location);
    url.searchParams.set('set_game_id',el.data('game_id'));
    window.location = url.href;
}

function update_game_score(ev){
    el = $(ev.target);
    game_id = el.data('game_id');
    home_input = $(".score_input[data-team='home']")
    away_input = $(".score_input[data-team='away']")
    score_home = Number(home_input.val());
    score_away = Number(away_input.val());
    if (!Number.isInteger(score_home)){
        alert(`Cannot set non integer value ${score_home} as home team's score`)
        return
    }
    if (!Number.isInteger(score_away)){
        alert(`Cannot set non integer value ${score_away} as away team's score`)
        return
    }
    $.post(`/set_scores/update/${game_id}`, {home: score_home, away: score_away})
    .done(cancel_set_score)
    .fail(function(e){alert(e.responseText)});
}



$(document).ready(function(){
    $('#randomize_scores').click(randomize_scores);
    $('.delete_btn').click(reset_game_score);
    $('.edit_btn').click(go_to_set_game_score);
    $('.cancel_set_score_btn').click(cancel_set_score);
    $('.confirm_set_score_btn').click(update_game_score);
})