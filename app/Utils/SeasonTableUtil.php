<?php

namespace App\Utils;

class SeasonTableUtil
{
    static function cmp_team_rows($team_a, $team_b){
        $points_a = $team_a['points'];
        $points_b = $team_b['points'];
        if ($points_a != $points_b) {
            return ($points_a > $points_b) ? -1 : 1;
        }
        $gf_a = $team_a['goals_for'];
        $ga_a = $team_a['goals_against'];
        $gd_a = $gf_a - $ga_a;
        $gf_b = $team_b['goals_for'];
        $ga_b = $team_b['goals_against'];
        $gd_b = $gf_b - $ga_b;
        if ($gd_a != $gd_b){
            return ($gd_a > $gd_b) ? -1 : 1;
        }
        if ($gf_a != $gf_b){
            return ($gf_a > $gf_b) ? -1 : 1;
        }
        return 0;
    }
        
    
    static function get_table($games, $competing_teams=[]){
        $relevant_games = $games->filter(function($game) use($competing_teams){
            return in_array($game->home_team_id, $competing_teams) && in_array($game->away_team_id, $competing_teams);
        });
        $table = array();
        foreach ($competing_teams as $team_id){
            if ( !in_array($team_id, array_keys($table)) ){
                $table[$team_id] = array(
                    'id'=>$team_id,
                    'points'=>0,
                    'games'=>0,
                    'wins'=>0,
                    'draws'=>0,
                    'loses'=>0,
                    'goals_for'=>0,
                    'goals_against'=>0
                );
            }
        }
        foreach($relevant_games as $game){
            $home_team_id = $game->home_team_id;
            $away_team_id = $game->away_team_id;
            $score_home = $game->home_score;
            $score_away = $game->away_score;
            
            $table[$home_team_id]['games'] += 1;
            $table[$home_team_id]['goals_for'] += $score_home;
            $table[$home_team_id]['goals_against'] += $score_away;
            $table[$away_team_id]['games'] += 1;
            $table[$away_team_id]['goals_for'] += $score_away;
            $table[$away_team_id]['goals_against'] += $score_home;
            if ($score_home > $score_away){
                $table[$home_team_id]['wins'] += 1;
                $table[$home_team_id]['points'] += 3;
                $table[$away_team_id]['loses'] += 1;
            } elseif ($score_home < $score_away){
                $table[$away_team_id]['wins'] += 1;
                $table[$away_team_id]['points'] += 3;
                $table[$home_team_id]['loses'] += 1;
            } else{
                $table[$home_team_id]['draws'] += 1;
                $table[$home_team_id]['points'] += 1;
                $table[$away_team_id]['draws'] += 1;
                $table[$away_team_id]['points'] += 1;
            }
        }
        usort($table, [self::class, 'cmp_team_rows']);
        array_walk($table, function(&$team_row, $index){
            $team_row['rank'] = $index + 1;
            $team_row['goals_diff'] = $team_row['goals_for'] - $team_row['goals_against'];
            $team_row = collect($team_row);
        });
        $table_rows = collect($table);
        $equal_teams = collect([]);
        foreach($table_rows->keys() as $index){
            if ($index == $table_rows->count() - 1){
                break;
            }
            $row_a = $table_rows->get($index);
            $row_b = $table_rows->get($index+1);
            if ([self::class, 'cmp_team_rows']($row_a, $row_b) == 0){
                if ( $equal_teams->isEmpty() || !$equal_teams->last()->contains($row_a['id']) ){
                    $equal_teams->push( collect([ $row_a['id'], $row_b['id'] ]) );
                } else {
                    $equal_teams->last()->push($row_b['id']);
                }
            }
        }
        if (!$equal_teams->isEmpty()){
            $equal_teams->each(function($teams_collection) use($table_rows, $competing_teams, $relevant_games){
                $base_rank = $table_rows->first(function($row) use($teams_collection){
                    return $row['id'] == $teams_collection->first();
                })['rank'];
                if ($teams_collection->values()->all() == $competing_teams){
                    $table_rows->each(function(&$row, $index) use($base_rank, $table_rows){
                        $row['rank'] = 1;
                    });
                } else {
                    $inner_table = [self::class, 'get_table']($relevant_games, $teams_collection->values()->all());
                    foreach($inner_table as $inner_row){
                        $team_row = $table_rows->first(function($row) use($inner_row){
                            return $row['id'] == $inner_row['id'];
                        });
                        $team_row['rank'] = $base_rank - 1 + $inner_row['rank'];
                    }
                }
            });
        }
        $table_rows->sortBy('rank');
        return $table_rows->values()->all();
    }
    
}