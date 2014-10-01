'use strict';

angular.module('myApp.rowers', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/rowers', {
    templateUrl: 'rowers/rowers.html',
    controller: 'RowersCtrl'
  });
}])

.controller('RowersCtrl', [function() {

}]);