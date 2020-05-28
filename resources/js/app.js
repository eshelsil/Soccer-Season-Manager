window.app = angular.module('app', [])
app.factory('DisabledAdminViews', function(){
    var data = {
        'teams': false,
        'schedule': false,
        'scores': false,
        'users': false,
    }
    function set(view, bool){
        data[view] = bool;
    }
    function get(view){
        return data[view]
    }
    return {
        set,
        get
    }
})
app.run(function($rootScope) {
    $rootScope.logout = ()=>{
        document.getElementById('logout-form').submit();
    }
    $rootScope.delete_active_user = ()=>{
        $.post(`/api/users/${$rootScope.active_user_id}?_method=delete`)
        .done(()=>{
            $rootScope.logout();
        })
        .fail((e)=>{alert(e.responseText)});
    }
    $rootScope.format_select_options = (options, params = {})=>{
        if (!Array.isArray(options) && typeof options == 'object'){
            // allow implementation of {value1: label1, value2: label2} options
            res = Object.keys(options).reduce((output, key)=>{
                output.push({
                    value: key,
                    label: options[key]
                })
                return output
            }, [])
        } else if (typeof options[0] !== 'object'){
            // allow implementation of [val1, val2, val3] options
            res = options.map((val)=>{
                return {value: val, label: val}
            })
        } else {
            res = options
        }
        if (params.with_all){
            res.unshift({value: 'all', label: params.all_label ?? '---'})
        }
        return res
    }
    $rootScope.update_teams_data_inheritors = function(){
        if (!this.teams_by_id){
            throw new Error('"teams_by_id" variable must be set to scope before update_teams_data_inheritors function is called')
        }
        this.teams_count = Object.keys(this.teams_by_id).length
        this.weeks_per_round = this.teams_count - 1
        this.weeks_per_season = this.weeks_per_round * 2
    }
    $rootScope.update_table_filter_week = function(){
        round_filter = this.round_filter
        if (round_filter === 'all'){
            week_options = _.range(1, this.weeks_per_season)
        } else {
            first_week = this.weeks_per_round * (round_filter - 1) + 1
            week_options = _.range(first_week, first_week + this.weeks_per_round)
        }
        this.week_filter_options = $rootScope.format_select_options(week_options, {with_all: true})
        if (this.week_filter !== 'all' && week_options.indexOf(Number(this.week_filter)) == -1){
            this.week_filter = 'all'
        }
    }
    $rootScope.update_table_filters_attrs = function(){
        this.round_filter_options = $rootScope.format_select_options([1,2], {with_all: true})
        this.team_filter_options = $rootScope.format_select_options(this.teams_by_id, {with_all: true})
        this.update_table_filter_week()
        this.$watch('round_filter', _.bind($rootScope.update_table_filter_week, this))
    }
    $rootScope.reset_table_filters = function(){
        filters_map = this.get_table_filters_map()
        for (model of Object.keys(filters_map)){
            null_values = filters_map[model]['null_values'] ?? [];
            this[model] = null_values[0];
        }
    }
    $rootScope.get_filtered_table_rows = function(table_rows){
        filters_map = this.get_table_filters_map()
        filter_func = (row) => {
            for (model of Object.keys(filters_map)){
                filter_attrs = filters_map[model];
                filter_cols = filter_attrs['table_keys']
                if (typeof filter_cols === 'string'){
                   filter_cols = [filter_cols]
                }
                match_values = filter_cols.map(key=> String(row[key]))
                filter_val = this[model]
                if (filter_attrs['null_values'].indexOf(filter_val) == -1 && match_values.indexOf(filter_val) == -1 ){
                    return false
                }
            }
            return true
        }
        filtered_rows = table_rows.filter(row => filter_func(row))
        return filtered_rows
    }
    $rootScope.get_table_filters_map = function(){
        return {
            'team_filter': {
                query_param: 'team',
                table_keys: ['home_team_id', 'away_team_id'],
                null_values: ['all']
            },
            'round_filter': {
                query_param: 'round',
                table_keys: 'round',
                null_values: ['all']
            },
            'week_filter': {
                query_param: 'week',
                table_keys: 'week',
                null_values: ['all']
            }
        }
    }
    $rootScope.bind_filters_to_table = function(update_filtered_rows){
        filters_map = this.get_table_filters_map()
        for (model of Object.keys(filters_map)){
            this.$watch(model, update_filtered_rows)
        }
    }
    $rootScope.bind_table_filters_to_url = function(){
        filters_map = this.get_table_filters_map()
        url = new URL(window.location);
        serach_params = url.searchParams;
        for (model of Object.keys(filters_map)){
            filter_attrs = filters_map[model];
            query_param = serach_params.get(filter_attrs['query_param'])
            null_value = (filter_attrs['null_values'] ?? [])[0]
            this[model] = query_param !== null ? query_param : null_value; 
            this.bind_model_to_query_param(model)
        }
    }
    $rootScope.get_search_query_with_filters = function(){
        url = new URL(window.location)
        filters_map = this.get_table_filters_map()
        for (model of Object.keys(filters_map)){
            key = filters_map[model]['query_param']
            null_values = filters_map[model]['null_values']
            val = this[model]
            if (val === undefined || null_values.indexOf(val) > -1){
                url.searchParams.delete(key)
            } else {
                url.searchParams.set(key, val)
            }
        }
        return url.search
    }
    $rootScope.bind_model_to_query_param = function(model){
        //NOTE used to not work - url got reset when openeing a select_input. could not reproduce bug
        update_url = (newVal) => {
            let {protocol, host, pathname} = window.location
            current_path = `${protocol}//${host}${pathname}`;
            url = new URL(`${current_path}${this.get_search_query_with_filters()}`)
            window.history.replaceState({}, "", url.href);
        }
        this.$watch(model, update_url)
    }
});

require('./bootstrap');
require('./games');
require('./set_teams');
require('./schedule_games');
require('./set_scores');
require('./manage_users');
require('./season_table');
require('./admin_menu');


$(document).ready(function(){
    let csrf_tkn = $('input[name="_token"]').val();
    let api_tkn = $('input[name="_api_token"]').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrf_tkn,
            'Authorization': `Bearer ${api_tkn}`,
      }
    });
})