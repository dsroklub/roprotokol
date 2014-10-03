'use strict';

var app = angular.module('myApp', [
  'ngRoute',
  'ngSanitize',
  'ui.bootstrap',
  'ui.select',
  'ngQuickDate',
  'myApp.version',
  'myApp.range',
  'myApp.database'
]).
config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/boat/checkout/:boat_id', {
    templateUrl: 'templates/boat/checkout.html',
    controller: 'BoatCtrl'
  });
  $routeProvider.when('/boat/categoryoverview/:boat_category_id?', {
    templateUrl: 'templates/boat/categoryoverview.html',
    controller: 'BoatCtrl'
  });
  $routeProvider.otherwise({redirectTo: '/boat/categoryoverview'});
}])
.config(['uiSelectConfig', function(uiSelectConfig) {
  uiSelectConfig.theme = 'bootstrap';
}]);

;
