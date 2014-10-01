'use strict';

angular.module('myApp.damages', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/damages', {
    templateUrl: 'damages/damages.html',
    controller: 'DamagesCtrl'
  });
}])

.controller('DamagesCtrl', [function() {

}]);