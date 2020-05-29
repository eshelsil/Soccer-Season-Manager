app.controller('season_table', ['$scope', function($scope) {
    $scope.get_table_filters_map = function(){
        return {
            'until_week_filter': {
                query_param: 'week',
                table_keys: 'week',
                null_values: ['all']
            },
        }
    }
    $scope.initialize = function(options){
        $scope.last_week = options.last_week
        url = new URL(window.location);
        serach_params = url.searchParams;
        $scope.until_week_filter = serach_params.get('week') !== null ? serach_params.get('week') : 'all';
        $scope.update_filter_options()
        $scope.bind_table_filters_to_url()
        $scope.bind_filter_to_page_reload()
    }
    $scope.bind_filter_to_page_reload = function(){
        let first_run = true;
        reload_upon_filter_change = ()=>{
            if (first_run){
                first_run = false
                return
            }
            window.location.reload()
        }
        $scope.$watch('until_week_filter', reload_upon_filter_change)
    }
    $scope.update_filter_options = function(){
        available_weeks = _.range(1, $scope.last_week + 1)
        this.until_week_options = this.format_select_options(available_weeks, {with_all: true})
    }
}]);