'use strict';

var rv=2;

var coxApp = angular.module('coxApp', [
  'ngRoute',
  'ngSanitize',
  'ui.bootstrap',
  'ui.select',
  'angular-momentjs',
  'ngDialog',
  'ngTable',
  'rowApp.version',
  'rowApp.range',
  'coxApp.database',
  'rowApp.utilities',
  'angular-confirm',
  'ui.bootstrap',
  'ui.bootstrap.datetimepicker',
  'angular.filter',
  'checklist-model',
  'coxApp.database'
//  ,'ds.clock'
])
.config([
  '$locationProvider', function($locationProvider) {
    $locationProvider.html5Mode(true);
  }])

.config([
  '$routeProvider', function($routeProvider) {
    $routeProvider.when('/signup/', {
      templateUrl: 'templates/signup.html?v='+rv,
      controller: 'coxCtrl'
    });
    $routeProvider.when('/admin/', {
      templateUrl: 'templates/admin.html?v='+rv,
      controller: 'coxCtrl'
	});
    $routeProvider.when('/login/', {
      templateUrl: 'templates/login.html?v='+rv,
      controller: 'noRight'
	});
    $routeProvider.when('/log/', {
      templateUrl: 'templates/log.html?v='+rv,
      controller: 'logCtrl'
	});
    $routeProvider.when('/requirements/', {
      templateUrl: 'templates/requirements.html?v='+rv,
      controller: 'coxCtrl'
    });
    $routeProvider.when('/', {redirectTo: '/signup'});
    $routeProvider.otherwise({
      templateUrl: 'templates/notimplementet.html?v='+rv,
    });
  }])
    .config(['$qProvider', function ($qProvider) {
      $qProvider.errorOnUnhandledRejections(false);
    }])
    .config(['uiSelectConfig', function(uiSelectConfig) {
      uiSelectConfig.theme = 'bootstrap';
    }]);

