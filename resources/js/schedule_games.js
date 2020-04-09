function auto_schedule(){
    $.post('/manage/auto_schedule')
    .done(()=>{window.location.reload()})
    .fail(function(e){alert(e.responseText)});
}

function go_to_set_scores(){
    window.location = '/set_scores';
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



$(document).ready(function(){
    let csrf_tkn = $('input[name="_token"]').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrf_tkn
      }
    });
    $('#auto_schedule').click(auto_schedule);
    $('#drop_games_table').click(drop_games_table);
    $('#to_set_score').click(go_to_set_scores);
})