app.controller('admin_menu', function($scope, DisabledAdminViews) {
    $scope.is_view_disabled = (view) =>{
        return DisabledAdminViews.get(view)
    }
});