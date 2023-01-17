'use strict';

angular.module('gymApp', [
  'ngRoute',
  'ngSanitize',
  'ui.bootstrap',
  'ui.select',
  'angular-momentjs',
  'ngDialog',
  'ngTable',
  'gym.version',
  'gym.database',
  'dsrcommon.utilities.onlynumber',
  'dsrcommon.utilities.nodsr',
  'dsrcommon.utilities.txttotime',
  'dsrcommon.utilities.totime',
  'dsrcommon.utilities.ifNull',
  'dsrcommon.utilities.split',
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
  '$locationProvider', function($locationProvider) {
    $locationProvider.html5Mode(true);
  }])
.config([
      '$routeProvider', function($routeProvider) {
	$routeProvider.when('/registrer/', {
	  templateUrl: 'templates/gym/checkout.html',
	  controller: 'teamCtrl'
	});
	$routeProvider.when('/registrer2', {
	  templateUrl: 'templates/gym/checkout2.html',
	  controller: 'teamCtrl'
	});
	$routeProvider.when('/admin/', {
	  templateUrl: 'templates/gym/admin.html',
	  controller: 'teamCtrl'
	});
	$routeProvider.when('/om/', {
	  templateUrl: 'templates/gym/om.html',
	  controller: 'teamCtrl'
	});
	$routeProvider.when('/', {redirectTo: '/registrer'});
	$routeProvider.otherwise(
          {redirectTo: '/registrer'}
        );
      }])
    .config(['uiSelectConfig', function(uiSelectConfig) {
      uiSelectConfig.theme = 'bootstrap';
    }])
;

