'use strict';

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
      templateUrl: 'templates/signup.html',
      controller: 'coxCtrl'
    });
    $routeProvider.when('/admin/', {
      templateUrl: 'templates/admin.html',
      controller: 'coxCtrl'
	});
    $routeProvider.when('/login/', {
      templateUrl: 'templates/login.html',
      controller: 'noRight'
	});
    $routeProvider.when('/requirements/', {
      templateUrl: 'templates/requirements.html',
      controller: 'coxCtrl'
    });
    $routeProvider.when('/', {redirectTo: '/signup'});
    $routeProvider.otherwise({
      templateUrl: 'templates/notimplementet.html',
    });
  }])
    .config(['uiSelectConfig', function(uiSelectConfig) {
      uiSelectConfig.theme = 'bootstrap';
    }]);

