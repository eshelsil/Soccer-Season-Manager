// window.app = angular.module('app', [], function($interpolateProvider) {
//     $interpolateProvider.startSymbol('<%');
//     $interpolateProvider.endSymbol('%>');
// });
window.app = angular.module('app', [])
app.run(function($rootScope) {
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
        this.team_filter = 'all'
        this.round_filter = 'all'
        this.week_filter = 'all'
    }
    $rootScope.bind_filters_to_table = function(get_table_rows, set_filtered_rows){
        filter_table_func = () =>{
            filtered_rows = get_table_rows().filter(game => {
                if (this.team_filter !== 'all' && [`${game.home_team_id}`, `${game.away_team_id}`].indexOf(this.team_filter) == -1 ){
                    return false
                }
                if (this.round_filter !== 'all' && this.round_filter != game.round ){
                    return false
                }
                if (this.week_filter !== 'all' && this.week_filter != game.week ){
                    return false
                }
                return true
            })
            set_filtered_rows(filtered_rows)
        }
        this.$watch('team_filter', filter_table_func)
        this.$watch('round_filter', filter_table_func)
        this.$watch('week_filter', filter_table_func)
    }
    $rootScope.bind_table_filters_to_url = function(){
        this.bind_model_to_query_param('team_filter', 'team', ['all'])
        this.bind_model_to_query_param('round_filter', 'round', ['all'])
        this.bind_model_to_query_param('week_filter', 'week', ['all'])
    }
    $rootScope.bind_model_to_query_param = function(model, key, null_values = []){
        this.$watch(model, (newVal) => {
            url = new URL(window.location)
            if (newVal === undefined || null_values.indexOf(newVal) > -1){
                url.searchParams.delete(key)
            } else {
                url.searchParams.set(key, newVal)
            }
            function update_url(){
                window.history.replaceState({}, `${model}_bind_to_${key}`,url.href)
            }
            setTimeout(update_url,0)
        })
    }
});
    // .directive('selectInput', function(){
    //     return {
    //         restrict: "E",
    //         // scope: {
    //         //     optionsFunc: "="
    //         // },
    //         link: function (scope, element, attrs){
                // options = scope[attrs.optionsFunc]()
                // if (!Array.isArray(options) && typeof options == 'object'){
                //     // allow implementation of {value1: label1, value2: label2} options
                //     options = Object.keys(options).reduce((key, output)=>{
                //         output.push({
                //             value: key,
                //             label: options[key]
                //         })
                //         return output
                //     }, [])
                // } else if (typeof options[0] !== 'object'){
                //     // allow implementation of [val1, val2, val3] options
                //     options = options.map((val)=>{
                //         return {value: val, label: val}
                //     })
                // }
                // if (attrs.withAllOption){
                //     options.unshift({value: 'all', label: attrs.allLabel ?? '---'})
                // }

    //             html = `<label for="${attrs.id}" class="col pl-0">${attrs.label}</label>`
    //             html += `<select id="${attrs.id}" ng-model="${attrs.ngModel}" class="custom-select" style="width:auto;">`
    //             initial_value = attrs.initVal ?? options[0].value;
    //             for (option of options){
    //                 html+= `<option value="${option.value}" ${initial_value == option.value ? 'selected' : ''}>${option.label}</option>`
    //             }
    //             html += `</select>`
    //             element.html(html)
    //         }
    //     }
    // })

require('./bootstrap');
// require('./snippets');
require('./games');
require('./set_teams');
require('./schedule_games');
require('./set_scores');
require('./reset');


$(document).ready(function(){
    let csrf_tkn = $('input[name="_token"]').val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrf_tkn
      }
    });
})