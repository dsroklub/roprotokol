'use strict';

var gymApp = angular.module('gymApp', [
  'ngRoute',
  'ngSanitize',
  'ui.bootstrap',
  'ui.select',
  'angular-momentjs',
  'ngDialog',
  'ngTable',
  'myApp.version',
  'myApp.range',
  'myApp.database',
  'myApp.utilities',
  'angular-confirm',
  'ui.bootstrap',
  'ui.bootstrap.datetimepicker',
  'angular.filter',
  'ds.clock'
])

/*
.config(function($locationProvider) { // OAuth html5 mode seems to break our routing
  $locationProvider.html5Mode(true).hashPrefix('#');
})
*/
.config([
      '$routeProvider', function($routeProvider) {
	$routeProvider.when('/registrer/', {
	  templateUrl: 'templates/gym/checkout.html',
	  controller: 'teamCtrl'
	});
	$routeProvider.when('/admin/', {
	  templateUrl: 'templates/gym/admin.html',
	  controller: 'teamCtrl'
	});
	$routeProvider.when('/', {redirectTo: '/ud'});
	$routeProvider.otherwise({
	  templateUrl: 'templates/notimplementet.html',
	});
      }])
    .config(['uiSelectConfig', function(uiSelectConfig) {
      uiSelectConfig.theme = 'bootstrap';
    }])
;

