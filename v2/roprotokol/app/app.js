'use strict';

// Declare app level module which depends on views, and components
angular.module('myApp', [
  'ngRoute',
  'myApp.register',
  'myApp.overview',
  'myApp.statistics',
  'myApp.rowers',
  'myApp.damages',
  'myApp.corrections',
  'myApp.admin',
  'myApp.version',
  'myApp.range'
]).
config(['$routeProvider', function($routeProvider) {
  $routeProvider.otherwise({redirectTo: '/register'});
}]);
