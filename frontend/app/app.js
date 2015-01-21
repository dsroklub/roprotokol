'use strict';

var app = angular.module('myApp', [
  'ngRoute',
  'ngSanitize',
  'ui.bootstrap',
  'ui.select',
  'ngQuickDate',
  'ngDialog',
  'ngTable',
  'myApp.version',
  'myApp.range',
  'myApp.database',
  'myApp.utilities'
]).
    config([
      '$routeProvider', function($routeProvider) {
	$routeProvider.when('/boat/checkout/:boat_id', {
	  templateUrl: 'templates/boat/checkout.html',
	  controller: 'BoatCtrl'
	});
	$routeProvider.when('/categoryoverview/', {
	  templateUrl: 'templates/boat/categoryoverview.html',
	  controller: 'BoatCtrl'
	});
	$routeProvider.when('/rowers/', {
	  templateUrl: 'templates/rowers/rower.html',
	  controller: 'RowerCtrl'
	});
	$routeProvider.when('/damages/', {
	  templateUrl: 'templates/damages.html',
	  controller: 'BoatCtrl'
	});
	$routeProvider.when('/boat/categoryoverview/', {
	  templateUrl: 'templates/boat/categoryoverview.html',
	  controller: 'BoatCtrl'
	});
	$routeProvider.when('/statoverview/', {
	  templateUrl: 'templates/stats/statoverview.html',
	  controller: 'StatCtrl'
	});
	$routeProvider.when('/', {redirectTo: '/boat/categoryoverview'});
	$routeProvider.otherwise({
	  templateUrl: 'templates/notimplementet.html',
	});
      }])
    .config(['uiSelectConfig', function(uiSelectConfig) {
      uiSelectConfig.theme = 'bootstrap';
    }]);
