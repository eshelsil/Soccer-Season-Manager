function randomize_scores(){
    $.post('/set_scores/randomize')
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
    $('#randomize_scores').click(randomize_scores);
})