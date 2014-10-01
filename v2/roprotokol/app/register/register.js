'use strict';

angular.module('myApp.register', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/register', {
    templateUrl: 'register/register.html',
    controller: 'RegisterCtrl'
  });
}])

.controller('RegisterCtrl', ['$scope', function($scope) {
  $scope.boattypes = [
      {
          'name': 'Inrigger 2+'
      },
      {
          'name': 'Inrigger 4+'
      },
      {
          'name': 'Gig 2x'
      },
      {
          'name': 'Gig 3x'
      },
      {
          'name': 'Git/Outrig 4+ og 4-'
      },
      {
          'name': 'Git/Outrig 4x'
      },
      {
          'name': 'Git/Outrig 8+'
      },
      {
          'name': 'Sculler 1x'
      },
      {
          'name': 'Sculler 2x'
      },
      {
          'name': 'Svava 1x'
      },
      {
          'name': 'Kajak 1'
      },
      {
          'name': 'Kajak 2'
      },
      {
          'name': 'Motorbåde'
      }
  ];
}]);