function go_to_schedule(){
    window.location = '/admin/schedule';
}

app.controller('teams_registration', ['$scope', 'DisabledAdminViews', function($scope, DisabledAdminViews) {
    $scope.go_to_schedule = go_to_schedule;
    $scope.initialize = function(options){
        $scope.default_teams = options.default_teams
        $scope.teams = {}
        $.get(`/api/teams`)
        .done((res)=>{
            for (team_object of res){
                $scope.teams[team_object.id] = team_object;
            }   
            $scope.update_and_apply();
        })
    };
    $scope.update_disabled_views = () => {
        DisabledAdminViews.set('teams', false)
        DisabledAdminViews.set('scores', true)
        DisabledAdminViews.set('schedule', !$scope.can_start_scheduling())
    }
    $scope.update_and_apply = ()=>{
        $scope.update_disabled_views()
        $scope.$apply()
    }
    $scope.can_start_scheduling = ()=>{
        teams_count = Object.keys($scope.teams).length
        return teams_count >=4 && teams_count % 2 == 0
    }
    $scope.remove_team = function(id){
        $.post(`/api/teams/${id}?_method=delete`)
        .done(()=>{
            delete($scope.teams[id]);
            $scope.update_and_apply();
        })
        .fail((e)=>{alert(e.responseText)});
    };
    $scope.remove_all_teams = function(){
        return $.post(`/api/teams/reset_all?_method=delete`)
        .done(()=>{
            $scope.teams = {};
            $scope.update_and_apply();
        })
        .fail((e)=>{alert(e.responseText)});
    };
    $scope.add_team = function(){
        name = $scope.new_team_input;
        if (!$scope.add_team_from.team_name.$valid){
            alert('Name input is required');
            return
        }
        $.post(`/api/teams`, {teams: [{name}]})
        .done((new_teams)=>{
            team_object = new_teams[0];
            $scope.teams[team_object.id] = team_object;
            $scope.new_team_input = '';
            $scope.update_and_apply();
        })
        .fail((e)=>{alert(e.responseText)});
    };
    $scope.use_deafult_teams = function(){
        $scope.remove_all_teams()
        .done(()=>{
            teams_array = $scope.default_teams.map( name=>{ return {name} })
            $.post(`/api/teams`, {teams: teams_array})
            .done((new_teams)=>{
                for(team_object of new_teams){
                    $scope.teams[team_object.id] = team_object;
                }
                $scope.update_and_apply();
            })
            .fail((e)=>{alert(e.responseText)});
        })
    };

}]);