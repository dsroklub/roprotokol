'use strict';

coxApp.controller(
  'noRight',
  ['$scope', '$routeParams', 'BasicService','$filter', 'ngDialog','orderByFilter','$log',
   function ($scope, $routeParams, BasicService, filter, ngDialog, orderBy,$log) {
     $scope.aspirants = null;
     $scope.sortAspirants = 'team';
     
     $scope.getpw = function() {
       if ($scope.login && $scope.login.aspirant) {
         BasicService.getpw($scope.login.aspirant);
       }
     }

     
   }
  ]
);
