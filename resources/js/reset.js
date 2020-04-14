

function reset_scores(){
    $.post('/set_scores/reset')
    .done(()=>{
        $('#reset_scores_dismiss_modal').click();
    })

    //#NOTE improve error alerts
    .fail(function(e){alert(e.responseText)});
}

function reset_games(){
    $.post('/manage/reset_games')
    .done(()=>{
        $('#reset_games_dismiss_modal').click();
        window.location = '/manage';
    })
    .fail(function(e){alert(e.responseText)});
}



$(document).ready(function(){
    $('#reset_games_confirm').click(reset_games);
    $('#reset_scores_confirm').click(reset_scores);
})