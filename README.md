## About Season Manager 2020

Season Manager 2020 is a web application built with Laravel framework.<br>
The Application make it possible to schedule, update & follow a custom soccer league.

## Features
- View the season table
![Season Table Image](https://github.com/eshelsil/Soccer-Season-Manager/blob/dev/readme_img/season_table.png?raw=true)
- View the games of the season - with awesome filtering
![Games Table Image](https://github.com/eshelsil/Soccer-Season-Manager/blob/dev/readme_img/played_games_table.png?raw=true)
- Create the league schedule - register teams & schedule games (only availble for admins)
![Games Table Image](https://github.com/eshelsil/Soccer-Season-Manager/blob/dev/readme_img/schedule_games.png?raw=true)
- Update games scores (only availble for admins)
![Games Table Image](https://github.com/eshelsil/Soccer-Season-Manager/blob/dev/readme_img/set_score.png?raw=true)`
- Manage registered users (only availble for admins)

## Getting Started
In order to get start, the app should be served on a host.<br>
When first served, the app will show a login/register page.<br>
The first registered user is an 'admin' user. All following registered users are 'regular' users.<br>
An admin user may remove other users and change their roles (from regular to admin and vice versa).<br>
Every user may delete itself (in case he is not the only admin user).<br>
The admin user should register teams for the season (under 'Admin Zone' tab on the menu).<br>
Then he should schedule the games for the season.<br>
Afterwards he can update the score of games whenever the are finished.<br>
Regular users may see the Season Table & the Games of the season.

## Supported Competitions
The app currently support the following competition types:
- [round-robin](https://en.wikipedia.org/wiki/Round-robin_tournament) season of 2 rounds


## Example
Deployed example may be seen here:
https://season-manager-2020-dev.herokuapp.com/
