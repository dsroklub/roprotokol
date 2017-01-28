'use strict';

var coxApp = angular.module('coxApp', [
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
  'checklist-model',
  'ds.clock'
])

/*
.config(function($locationProvider) { // OAuth html5 mode seems to break our routing
  $locationProvider.html5Mode(true).hashPrefix('#');
})
*/
.config([
      '$routeProvider', function($routeProvider) {
	$routeProvider.when('/signup/', {
	  templateUrl: 'templates/cox/signup.html',
	  controller: 'coxCtrl'
	});
	$routeProvider.when('/admin/', {
	  templateUrl: 'templates/cox/admin.html',
	  controller: 'coxCtrl'
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
