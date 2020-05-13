function auto_schedule(){
    $.post('/schedule/auto')
    .done(()=>{window.location.reload()})
    .fail(function(e){alert(e.responseText)});
}

// function schedule_game(){
//     week = $('#setWeekSelect').val();
//     home_team_id = $('#homeTeamSelect').val();
//     away_team_id = $('#awayTeamSelect').val();
//     $.post('/api/games', {
//         week,
//         home_team_id,
//         away_team_id
//     })
//     .done(()=>{window.location.reload()})
//     .fail(function(e){alert(e.responseText)});
// }
// function delete_game(ev){
//     el = $(ev.target);
//     game_id = el.data('game_id')
//     $.post(`/api/games/${game_id}?_method=delete`)
//     .done(()=>{window.location.reload()})
//     .fail(function(e){alert(e.responseText)});
// }
function go_to_set_scores(){
    window.location = '/set_scores';
}

function go_to_set_teams(){
    window.location = '/set_teams';
}

// function truncate_games_table(){
//     //#NOTE this method exist in reset.js
    
//     $.post('/schedule/reset_games')
//     .done(()=>{
//         window.location.reload();
//     })
//     .fail(function(e){alert(e.responseText)});
// }

function reset_filters(){
    url = new URL(window.location);
    url.searchParams.delete('team_id');
    url.searchParams.delete('week');
    url.searchParams.delete('round');
    window.location = url.href;
}


app.controller('games_scheduler', function($scope, $location) {
    url = new URL(window.location);
    serach_params = url.searchParams;
    $scope.go_to_set_scores = go_to_set_scores;
    $scope.go_to_set_teams = go_to_set_teams;
    $scope.update_and_apply = ()=>{
        $scope.update_weeks_to_schedule()
        $scope.update_available_teams()
        $scope.update_filtered_games()
        $scope.reset_filters_if_no_table()
        $scope.$apply()
    }
    $scope.reset_filters_if_no_table = () =>{
        if (Object.keys($scope.games).length == 0){
            $scope.reset_table_filters()
        }
    }
    $scope.add_game = function(){
        h= $scope.home_team_input;
        a= $scope.away_team_input;
        w= $scope.set_week_input;
        console.log('h,a,w:', h,a,w)
        $.post(`/api/games`, {games: [{week: w, home_team_id: h, away_team_id: a}]})
        .done((new_games)=>{
            game_object = new_games[0];
            $scope.games[game_object.id] = game_object;
            $scope.update_and_apply();
        })
        .fail((e)=>{alert(e.responseText)});
    };
    $scope.count_games = function(){
        return Object.keys($scope.games || {}).length
    }
    $scope.has_games = function(){
        return $scope.count_games() > 0
    }
    $scope.update_weeks_to_schedule = function(){
        games_per_week = {};
        for (game of Object.values($scope.games)){
            if (games_per_week[game.week] == undefined){
                games_per_week[game.week] = 1;
            } else {
                games_per_week[game.week] += 1;
            }
        }
        available_weeks = [];
        for (index of Array(params.weeks_count).keys()){
            week = index + 1;
            console.log('week, games_per_week[week], params.teams_by_id / 2', week, games_per_week[week], params.teams_by_id / 2)
            console.log(games_per_week, params.teams_by_id)
            if (games_per_week[week] == Object.keys(params.teams_by_id).length / 2){
                continue;
            }
            console.log('here')
            available_weeks.push(week)
        }
        $scope.weeks_to_schedule = available_weeks
        $scope.update_week_input_options()
    }
    $scope.get_round_input = ()=>{
        games_per_ronund = Object.keys($scope.teams_by_id).length - 1
        round = Math.ceil($scope.set_week_input / games_per_ronund)
        return round
    }
    $scope.update_filtered_games = () =>{
        $scope.filtered_games = $scope.get_filtered_table_rows(Object.values($scope.games))
    }
    $scope.update_week_input_options = ()=>{
        $scope.week_input_options = $scope.weeks_to_schedule.map((week)=> {
            return {
                value: week,
                label: week,
            }
        })
        console.log('test', $scope.weeks_to_schedule, $scope.set_week_input)
        if ($scope.weeks_to_schedule.indexOf(Number($scope.set_week_input))){
            current_week = $scope.set_week_input ?? 0
            $scope.set_week_input = String($scope.weeks_to_schedule.find(week => week >= current_week))
        }
        console.log('$scope.set_week_input', $scope.set_week_input)
    }
    $scope.update_home_team_input_options = ()=>{
        $scope.home_team_options = $scope.available_teams.map((team_id)=> {
            return {
                value: team_id,
                label: $scope.teams_by_id[team_id],
            }
        })
        console.log($scope.home_team_input, '$scope.home_team_input')
        $scope.home_team_input = $scope.available_teams.indexOf($scope.home_team_input) > -1 ?
            $scope.home_team_input : String($scope.home_team_options[0].value)
    }
    $scope.update_away_team_input_options = ()=>{
        $scope.away_team_options = $scope.available_teams.map((team_id)=> {
            return {
                value: team_id,
                label: $scope.teams_by_id[team_id],
            }
        })
        $scope.away_team_input = $scope.available_teams.indexOf($scope.away_team_input) > -1 ?
            $scope.away_team_input : String($scope.away_team_options[1].value)
    }
    $scope.update_available_teams = ()=>{
        
        games_played = Object.values($scope.games).filter(game => game.week == $scope.set_week_input )
        teams_played = games_played.reduce((output, game)=>{
            output.push(String(game.home_team_id), String(game.away_team_id))
            return output
        }, [])
        $scope.available_teams = _.difference(Object.keys($scope.teams_by_id), teams_played)
        console.log('$scope.teams_by_id, teams_played', $scope.teams_by_id, teams_played)
        console.log('$scope.available_teams', $scope.available_teams)
        $scope.update_home_team_input_options()
        $scope.update_away_team_input_options()
    }
    $scope.get_games = ()=>{
        return Object.values($scope.games)
    }
    $scope.initialize = function(options){
        $scope.teams_by_id = options.teams_by_id
        $scope.update_teams_data_inheritors()
        $scope.team_filter = serach_params.get('team') !== null ? serach_params.get('team') : 'all';
        $scope.round_filter = serach_params.get('round') !== null ? serach_params.get('round') : 'all';
        $scope.week_filter = serach_params.get('week') !== null ? serach_params.get('week') : 'all';
        $scope.bind_table_filters_to_url()
        $scope.bind_filters_to_table($scope.update_filtered_games)
        $scope.update_table_filters_attrs()
        // $scope.update_table_filter_week()
        $scope.set_week_input = '1';
        $scope.games = {}
        $scope.home_team_options = [];
        $.get(`/api/games`)
        .done((res)=>{
            $scope.games = res;
            $scope.update_and_apply();
        })
    };
    $scope.remove_game = function(id){
        $.post(`/api/games/${id}?_method=delete`)
        .done(()=>{
            delete($scope.games[id]);
            $scope.update_and_apply();
        })
        .fail((e)=>{alert(e.responseText)});
    };
    $scope.remove_all_games = function(){
        $.post(`/api/games/reset_all?_method=delete`)
        .done(()=>{
            $scope.games = {};
            $scope.update_and_apply();
        })
        .fail((e)=>{alert(e.responseText)});
    };
});


$(document).ready(function(){
    $('#auto_schedule').click(auto_schedule);
    // $('#truncate_games_table').click(truncate_games_table);
    // $('#go_to_set_teams').click(go_to_set_teams);
    // $('#reset_filters').click(reset_filters);
    // $('#to_set_score').click(go_to_set_scores);
    // $('#schedule_game_button').click(schedule_game);
})