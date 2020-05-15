get_table = function(games, teams = []){
    relevant_games = games.filter(game => {
        return teams.includes(`${game['home_team_id']}`) && teams.includes(`${game['away_team_id']}`)
    })
    let table = {};
    for(team_id of teams ){
        table[team_id] = {
            id: team_id,
            points: 0,
            games: 0,
            wins: 0,
            draws: 0,
            loses: 0,
            goals_for: 0,
            goals_against: 0
        }
    }
    for (game of relevant_games){
        home_team_id = game['home_team_id'];
        away_team_id = game['away_team_id'];
        score_home = game['home_score'];
        score_away = game['away_score'];
        table[home_team_id]['games'] += 1;
        table[home_team_id]['goals_for'] += score_home;
        table[home_team_id]['goals_against'] += score_away;
        table[away_team_id]['games'] += 1;
        table[away_team_id]['goals_for'] += score_away;
        table[away_team_id]['goals_against'] += score_home;
        if (score_home > score_away){
            table[home_team_id]['wins'] += 1;
            table[home_team_id]['points'] += 3;
            table[away_team_id]['loses'] += 1;
        } else if (score_home < score_away){
            table[away_team_id]['wins'] += 1;
            table[away_team_id]['points'] += 3;
            table[home_team_id]['loses'] += 1;
        } else {
            table[home_team_id]['draws'] += 1;
            table[home_team_id]['points'] += 1;
            table[away_team_id]['draws'] += 1;
            table[away_team_id]['points'] += 1;
        }
    }
    function cmp(team_a, team_b){
        points_a = team_a['points'];
        points_b = team_b['points'];
        if (points_a != points_b) {
            return (points_a > points_b) ? -1 : 1;
        }
        gf_a = team_a['goals_for'];
        ga_a = team_a['goals_against'];
        gd_a = gf_a - ga_a;
        gf_b = team_b['goals_for'];
        ga_b = team_b['goals_against'];
        gd_b = gf_b - ga_b;
        if (gd_a != gd_b){
            return (gd_a > gd_b) ? -1 : 1;
        }
        if (gf_a != gf_b){
            return (gf_a > gf_b) ? -1 : 1;
        }
        return 0;
    }
    
    let table_rows = Object.values(table).sort(cmp).map((team_row, index)=>{
        return Object.assign(team_row, {
            rank: index + 1,
            goals_diff: team_row['goals_for'] - team_row['goals_against'],
        })
    })
    let equal_teams = []
    for (index in table_rows){
        index = Number(index)
        if (index == table_rows.length - 1){
            break
        }
        row_a = table_rows[index]
        row_b = table_rows[index+1]
        if (cmp(row_a, row_b) == 0){
            if (_.isEmpty(equal_teams) || _.last(equal_teams).indexOf(row_a.id) == -1){
                equal_teams.push([row_a.id, row_b.id])
            } else {
                _.last(equal_teams).push(row_b.id)
            }
        }
    }
    if (!_.isEmpty(equal_teams)){
        for (teams_array of equal_teams){
            let  base_rank = table_rows.find((row)=>row.id==teams_array[0]).rank
            if (_.isEqual(teams_array.sort(), teams.sort())){
                for (team_id of teams_array){
                    let team_row = table_rows.find((row)=>row.id==team_id)
                    team_row.rank = base_rank
                }
            } else {
                let inner_table = get_table(relevant_games, teams_array)
                for (inner_row of inner_table){
                    let team_row = table_rows.find((row)=>row.id==inner_row.id)
                    team_row.rank = base_rank - 1 + inner_row.rank
                }
            }
        }
    }
 
    return _.sortBy(table_rows, row=>row.rank)
}



app.controller('season_table', function($scope) {
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
        $scope.teams_by_id = options.teams_by_id ?? {}
        $scope.games = options.games ?? []
        url = new URL(window.location);
        serach_params = url.searchParams;
        $scope.until_week_filter = serach_params.get('week') !== null ? serach_params.get('week') : 'all';
        $scope.update_filter_options()
        $scope.bind_filters_to_table($scope.update_table)
        $scope.bind_table_filters_to_url()
        $scope.update_table()
    }
    $scope.update_filter_options = function(){
        last_week = Object.values($scope.games).reduce((max_week, game)=>{
            return Math.max(game.week,  max_week)
        }, 0)
        available_weeks = _.range(1, last_week + 1)
        this.until_week_options = this.format_select_options(available_weeks, {with_all: true})
    }
    $scope.get_filtered_table_rows = function(table_rows){
        filter_func = (row) => {
                filter_val = this.until_week_filter
                return filter_val === 'all' || Number(filter_val) >= row.week
        }
        return table_rows.filter(row => filter_func(row))
    }
    $scope.update_table = function(){
        games = $scope.get_filtered_table_rows($scope.games)
        teams_by_id = $scope.teams_by_id
        table_rows = get_table(games, Object.keys(teams_by_id))
        $scope.teams_table = table_rows.map((row, index)=>{
            name = teams_by_id[row.id]
            return Object.assign(row, {
                name,
                rank_sign: row.rank == index + 1 ? row.rank : '-'
            })
        })
    }
});