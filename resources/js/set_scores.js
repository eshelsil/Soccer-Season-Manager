app.controller('set_scores', function($scope, DisabledAdminViews) {
    $scope.home_input = {};
    $scope.away_input = {};
    $scope.games = {};
    url = new URL(window.location);
    serach_params = url.searchParams;
    $scope.is_on_played_tab = serach_params.get('is_done') == 1;

    $scope.has_available_games = function(){
        return Object.keys($scope.games).length > 0
    }
    $scope.edit_game = function(game){
        $scope.home_input[game.id] = game.is_done ? Number(game.home_score) : 0;
        $scope.away_input[game.id] = game.is_done ? Number(game.away_score) : 0;
        $scope.game_on_edit = game.id;
    };
    $scope.cancel_edit = function(){
        $scope.game_on_edit = undefined;
    };
    $scope.remove_game = function(id){
        $.post(`/api/games/${id}?_method=put`, {home: null, away: null})
        .done(()=>{
            delete($scope.games[id]);
            $scope.$apply();
        })
        .fail((e)=>{alert(e.responseText)});
    };
    $scope.set_score = (game_id)=>  {
        home_score = $scope.home_input[game_id];
        away_score = $scope.away_input[game_id];
        if (!Number.isInteger(home_score)){
            alert(`Cannot set non integer value ${home_score} as home team's score`)
            return
        }
        if (!Number.isInteger(away_score)){
            alert(`Cannot set non integer value ${away_score} as away team's score`)
            return
        }
        $.post(`/api/games/${game_id}?_method=put`, {home: home_score, away: away_score})
        .done((game_object)=>{
            $scope.games[game_object['id']] = Object.assign($scope.games[game_object['id']], game_object)
            if ($scope.is_on_played_tab){
                $scope.cancel_edit();
            } else {
                delete($scope.games[game_id]);
            }
            $scope.$apply();
        })
        .fail((e)=>{alert(e.responseText)});
    }
    $scope.get_games = function(){
        let {protocol, host, pathname} = window.location
        current_path = `${protocol}//${host}${pathname}`;
        url = new URL(`${current_path}${$scope.get_search_query_with_filters()}`)
        if (url.searchParams.get('is_done') != 1){
            url.searchParams.set('is_done', 0)
        }

        $.get(`/api/games/${url.search}`)
        .done((res)=>{
            $scope.games = res;
            $scope.$apply();
        })
    }
    $scope.randomize_all = ()=>{
        games_data = [];
        for (game_id in $scope.games){
            home_score = Math.floor(Math.random() * 5);
            away_score = Math.floor(Math.random() * 5);
            game = $scope.games[game_id]
            if (game.home_team_name == 'Hapoel Tel Aviv' && home_score < 2){
                home_score = Math.floor(Math.random() * 5);
            }
            if (game.away_team_name == 'Hapoel Tel Aviv' && away_score < 2){
                away_score = Math.floor(Math.random() * 5);
            }
            games_data.push({id: game_id, home: home_score, away: away_score})
        }
        $.post(`/api/games?_method=put`, {games: games_data})
        .done((games)=>{
            $('#randomize_scores_dismiss_modal').click();
            for (game_id in games){
                delete($scope.games[game_id])
            }
            $scope.$apply();
        })
        .fail((e)=>{alert(e.responseText)});
    }
    $scope.reset_all_scores = () => {
        games_data = Object.keys($scope.games).map(game_id=>({
            id: game_id,
            home: null,
            away: null
        }));
        $.post(`/api/games?_method=put`, {games: games_data})
        .done(()=>{
            $('#reset_scores_dismiss_modal').click();
            $scope.games = {};
            $scope.$apply();
        })
        //#NOTE improve error alerts
        .fail(function(e){alert(e.responseText)});
    }
    $scope.update_disabled_views = () => {
        DisabledAdminViews.set('teams', true)
        DisabledAdminViews.set('scores', false)
        DisabledAdminViews.set('schedule', false)
    }
    $scope.initialize = function(options){
        $scope.teams_by_id = options.teams_by_id
        $scope.update_disabled_views()
        $scope.update_teams_data_inheritors()
        $scope.bind_table_filters_to_url()
        $scope.update_table_filters_attrs()
        filters_map = $scope.get_table_filters_map()
        for (model in filters_map){
            $scope.$watch(model, $scope.get_games)
        }
    }
});