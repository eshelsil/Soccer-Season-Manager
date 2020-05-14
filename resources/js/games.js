shorten_result_map = {
    'draw': 'D',
    'win': 'W',
    'lose': 'L'
}

app.controller('games_display', function($scope) {
    $scope.is_team_selected = function(){
        return [undefined, 'all'].indexOf($scope.team_filter) === -1
    }
    $scope.get_shorten_result = function(result){
        return shorten_result_map[result] ?? ''
    }
    $scope.initialize = function(options){
        $scope.teams_by_id = options.teams_by_id ?? {}
        $scope.games = []
        $scope.update_teams_data_inheritors()
        $scope.update_table_filters_attrs()
        $scope.bind_filters_to_table($scope.update_table)
        $scope.bind_table_filters_to_url()
    }
    $scope.update_table = function(){
        let {protocol, host, pathname} = window.location
        current_path = `${protocol}//${host}${pathname}`;
        url = new URL(`${current_path}${$scope.get_search_query_with_filters()}`)
        url.searchParams.delete('is_done')

        $.get(`/api/games/${url.search}`)
        .done((res)=>{
            $scope.games = res;
            $scope.$apply();
        })
    }
});