function generate_first_round_order(ids){

    //explanation on method -> https://nrich.maths.org/1443
    _.shuffle(ids);
    middle_of_poligon = ids[0];
    ids.splice(0, 1);
    connections = {};
    weeks_count = ids.length;
    lower_index = 0;
    higher_index = weeks_count -1;
    is_higher_index_hosting = true;
    while (higher_index >= lower_index){
        if (higher_index == lower_index){
            connections["middle"] = higher_index;
            break;
        }
        if (is_higher_index_hosting){
            connections[higher_index] = lower_index;
        }else {
            connections[lower_index] = higher_index;
        }
        lower_index ++;
        higher_index --;
        is_higher_index_hosting = !is_higher_index_hosting;
    }
    games = [];
    for ( week of _.range(1, weeks_count + 1) ) {
        last_id = ids.pop();
        ids.unshift(last_id);
        
        for (polygon_pos_a in connections){
            polygon_pos_b = connections[polygon_pos_a]
            if (polygon_pos_a == "middle"){
                teams = [middle_of_poligon, ids[polygon_pos_b]];
                home_team_id = teams[week % 2];
                away_team_id = teams[(week + 1) % 2];
            } else {
                home_team_id = ids[polygon_pos_a];
                away_team_id = ids[polygon_pos_b];
            }
            games.push({
                round: 1,
                week,
                home_team_id,
                away_team_id
            });
        }
    }
    return games;
}

function generate_games(teams_by_id){
    team_ids = Object.keys(teams_by_id);
    first_round_games = generate_first_round_order(team_ids);
    last_week = _.last(first_round_games)["week"];
    second_round_games = [];
    for(game of first_round_games){
        second_round_games.push({
            "round": 2,
            "week": last_week + game["week"],
            "home_team_id": game["away_team_id"],
            "away_team_id": game["home_team_id"]
        })
    }
    return _.concat(first_round_games, second_round_games);
}

function go_to_set_scores(){
    window.location = '/set_scores';
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
        for (index of Array($scope.weeks_count).keys()){
            week = index + 1;
            if (games_per_week[week] == Object.keys($scope.teams_by_id).length / 2){
                continue;
            }
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
        if ($scope.weeks_to_schedule.indexOf(Number($scope.set_week_input))){
            current_week = $scope.set_week_input ?? 0
            $scope.set_week_input = String($scope.weeks_to_schedule.find(week => week >= current_week))
        }
    }
    $scope.update_home_team_input_options = ()=>{
        $scope.home_team_options = $scope.available_teams.map((team_id)=> {
            return {
                value: team_id,
                label: $scope.teams_by_id[team_id],
            }
        })
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
        $scope.update_home_team_input_options()
        $scope.update_away_team_input_options()
    }
    $scope.initialize = function(options){
        $scope.teams_by_id = options.teams_by_id
        $scope.weeks_count = options.weeks_count
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
    $scope.add_game = function(){
        h= $scope.home_team_input;
        a= $scope.away_team_input;
        w= $scope.set_week_input;
        $.post(`/api/games`, {games: [{week: w, home_team_id: h, away_team_id: a}]})
        .done((new_games)=>{
            game_object = new_games[0];
            $scope.games[game_object.id] = game_object;
            $scope.update_and_apply();
        })
        .fail((e)=>{alert(e.responseText)});
    };
    $scope.auto_schedule_all = function(){
        if (!_.isEmpty($scope.games)){
            alert("\"games\" table must be empty in order to auto schedule games")
            return 
        }
        games_list = generate_games($scope.teams_by_id)
        $.post(`/api/games`, {games: games_list})
        .done((new_games)=>{
            for(game_object of new_games){
                $scope.games[game_object.id] = game_object;
            }
            $scope.update_and_apply();
        })
        .fail((e)=>{alert(e.responseText)});
    };

});