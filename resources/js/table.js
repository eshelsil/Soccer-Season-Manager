SELECT_ID_TO_KEY = {
    'weekSelect': 'week'
};

function selectChange(ev){
    select = ev.target;
    key = SELECT_ID_TO_KEY[select.id];
    if (key === undefined){
        return;
    }
    val = select.value;
    url = new URL(window.location);
    if (val == 'all'){
        url.searchParams.delete(key);
    } else {
        url.searchParams.set(key, val);
        if (key == 'round'){
            weeks_per_round = $("#teamSelect").children().length - 2;
            min_available_week = (val-1) * weeks_per_round + 1;
            max_available_week = val * weeks_per_round;
            selected_week = url.searchParams.get('week');
            if (selected_week && (selected_week > max_available_week || selected_week < min_available_week) ){
                url.searchParams.delete('week');
            }
        }
    }
    window.location = url.href;
}

//#NOTE exists in games.js

$(document).ready(function(){
    $('select.custom-select').change(selectChange);
})