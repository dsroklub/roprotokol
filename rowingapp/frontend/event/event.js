'use strict';

var rv=13;
var eventApp = angular.module('eventApp', [
  'ngRoute',
  'ngSanitize',
  'ui.bootstrap',
  'ui.select',
  'angular-momentjs',
  'ngDialog',
  'ngTable',
  'rowApp.version',
  'rowApp.range',
  'eventApp.database',
  'rowApp.utilities',
  'angular-confirm',
  'ui.bootstrap',
  'ui.bootstrap.datetimepicker',
  'angular.filter',
  'checklist-model',
  'eventApp.database',
  'ngFileUpload'
])
.config([
  '$locationProvider', function($locationProvider) {
    $locationProvider.html5Mode(true);
  }])

.config([
  '$routeProvider', function($routeProvider) {
    $routeProvider.when('/eventsubscribe/', {
      templateUrl: 'templates/timeline.html',
      controller: 'eventCtrl'
    });
    $routeProvider.when('/forumsubscribe/', {
      templateUrl: 'templates/forum.html',
      controller: 'eventCtrl'
	});
    $routeProvider.when('/eventcreate/', {
      templateUrl: 'templates/eventcreate.html',
      controller: 'eventCtrl'
	});
    $routeProvider.when('/message/', {
      templateUrl: 'templates/message.html',
      controller: 'eventCtrl'
	});
    $routeProvider.when('/admin/', {
      templateUrl: 'templates/admin.html',
      controller: 'eventCtrl'
	});
    $routeProvider.when('/public/', {
      templateUrl: 'templates/public.html',
      controller: 'eventCtrl'
	});
    $routeProvider.when('/!#timeline/:event', {
      templateUrl: 'templates/timeline.html',
      controller: 'eventCtrl'
    });
    $routeProvider.when('/timeline/', {
      templateUrl: 'templates/timeline.html',
      controller: 'eventCtrl'
    });
    $routeProvider.when('/showevent/:event', {
      templateUrl: 'templates/timeline.html',
      controller: 'eventCtrl'
    });
    $routeProvider.when('/login/', {
      templateUrl: 'templates/login.html',
      controller: 'noRight'
	});
    $routeProvider.when('/', {redirectTo: '/login'});
    $routeProvider.otherwise({
      templateUrl: 'templates/notimplementet.html',
      controller: 'eventCtrl'
    });
  }])
    .config(['uiSelectConfig', function(uiSelectConfig) {
      uiSelectConfig.theme = 'bootstrap';
    }]);

