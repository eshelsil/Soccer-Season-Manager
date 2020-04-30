app.controller('set_scores', function($scope, $location) {
    $scope.home_input = {};
    $scope.away_input = {};
    url = new URL(window.location);
    serach_params = url.searchParams;
    $scope.getSearchParam = function(key){return serach_params.get(key)};
    $scope.selected_team_class = 'underlined';
    $scope.winner_class = 'font-weight-bold';
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
        $.post(`/api/games?_method=patch`, {games: games_data})
        .done((games)=>{
            for (game_id in games){
                delete($scope.games[game_id])
            }
            $scope.$apply();
        })
        .fail((e)=>{alert(e.responseText)});
    }
    $scope.is_on_played_tab = serach_params.get('is_done') == 1;
});