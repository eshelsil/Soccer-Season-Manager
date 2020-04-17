// window.app = angular.module('app', [], function($interpolateProvider) {
//     $interpolateProvider.startSymbol('<%');
//     $interpolateProvider.endSymbol('%>');
// });
window.app = angular.module('app', []);

require('./bootstrap');
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