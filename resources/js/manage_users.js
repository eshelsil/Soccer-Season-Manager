app.controller('manage_users', ['$scope', function($scope) {
    $scope.remove_user = function(id, with_logout = false){
        $.post(`/api/users/${id}?_method=delete`)
        .done(()=>{
            if (with_logout){
                $scope.logout();
                return
            }
            delete($scope.users[id]);
            $scope.$apply()
        })
        .fail((e)=>{alert(e.responseText)});
    };
    $scope.update_role = (user_id, role)=>  {
        uri_postfix = role == 'admin' ? 'set_admin' : 'set_regular';
        $.post(`/api/users/${user_id}/${uri_postfix}?_method=put`)
        .done((user_object)=>{
            $scope.users[user_object['id']] = Object.assign($scope.users[user_object['id']], user_object)
            $scope.$apply();
        })
        .fail((e)=>{alert(e.responseText)});
    }
    $scope.make_admin = (user_id)=>{
        $scope.update_role(user_id, 'admin')
    }
    $scope.make_regular = (user_id)=>{
        $scope.update_role(user_id, 'regular')
    }
    $scope.initialize = function(options){
        $scope.users = {};
        $.get(`/api/users`)
        .done((res)=>{
            for(user_object of res){
                $scope.users[user_object.id] = user_object;
            }
            $scope.$apply();
        })
    }
}]);