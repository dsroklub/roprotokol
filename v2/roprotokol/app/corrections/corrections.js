'use strict';

angular.module('myApp.corrections', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/corrections', {
    templateUrl: 'corrections/corrections.html',
    controller: 'CorrectionsCtrl'
  });
}])

.controller('CorrectionsCtrl', [function() {

}]);