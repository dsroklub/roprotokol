'use strict';

angular.module('myApp.register', ['ngRoute'])

.config(['$routeProvider', function($routeProvider) {
  $routeProvider.when('/register', {
    templateUrl: 'app/register/register.html',
    controller: 'RegisterCtrl'
  });
}])
// http://jsfiddle.net/pkozlowski_opensource/sJdzt/4/

.controller('RegisterCtrl', ['$scope', function($scope) {
  $scope.selected = [];
  
  $scope.destinations = [
      {
          'id': 1,
          'name': 'Bellevue',
          'distance': 15
      },
      {
          'id': 2,
          'name': 'Charlottenlund',
          'distance': 7
      },
      {
          'id': 3,
          'name': 'Flakfortet',
          'distance': 22
      }
  ];
  
  $scope.triptypes = [
      {
          'id': 1,
          'name': 'Lokaltur'
      }
  ];
  
  $scope.boattypes = [
      {
          'id': 1,
          'name': 'Inrigger 2+',
          'boats': [
              {
                  'name': 'Ask',
                  'status': 'Ok'
              },
              {
                  'name': 'Bjarke',
                  'status': 'OK'
              }
          ]
      },
      {
          'id': 2,
          'name': 'Inrigger 4+'
      },
      {
          'id': 3,
          'name': 'Gig 2x'
      },
      {
          'id': 4,
          'name': 'Gig 3x'
      },
      {
          'id': 5,
          'name': 'Git/Outrig 4+ og 4-'
      },
      {
          'id': 6,
          'name': 'Git/Outrig 4x'
      },
      {
          'id': 7,
          'name': 'Git/Outrig 8+'
      },
      {
          'id': 8,
          'name': 'Sculler 1x'
      },
      {
          'id': 9,
          'name': 'Sculler 2x'
      },
      {
          'id': 10,
          'name': 'Svava 1x'
      },
      {
          'id': 11,
          'name': 'Kajak 1'
      },
      {
          'id': 12,
          'name': 'Kajak 2'
      },
      {
          'id': 13,
          'name': 'Motorbåde'
      }
  ];
  
  $scope.select = function(boats) {
      console.log("hello");
      $scope.selected = boats;
  };
  
  var now = new Date();
  
  $scope.checkout = {
      'startime' : now,
      'expectedtime': new Date(now.getTime() + 60000 * 60),
      'endtime': '',
      'triptype': $scope.triptypes[0],
      'rowers': []
  };
   
}]);