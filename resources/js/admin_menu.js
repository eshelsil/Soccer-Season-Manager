app.controller('admin_menu', ['$scope', 'DisabledAdminViews', function($scope, DisabledAdminViews) {
    $scope.is_view_disabled = (view) =>{
        return DisabledAdminViews.get(view)
    }
}]);