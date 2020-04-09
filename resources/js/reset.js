

function reset_scores(){
    $.post('/set_scores/reset')
    .done(()=>{
        $('#reset_scores_dismiss_modal').click();
    })
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
    let csrf_tkn = $('input[name="_token"]').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrf_tkn
      }
    });
    $('#reset_games_confirm').click(reset_games);
    $('#reset_scores_confirm').click(reset_scores);
})