'use strict';

eventApp.controller(
  'noRight',
  ['$scope', '$routeParams', 'BasicService','$filter', 'ngDialog','orderByFilter','$log',
   function ($scope, $routeParams, BasicService, filter, ngDialog, orderBy,$log) {
     $scope.loginstatus= "";
     
     $scope.logout = function() {
       BasicService.logout();;
     }
     
     $scope.getpw = function() {
       if ($scope.login && $scope.login.member) {
         BasicService.getpw($scope.login.member);
       }
     }     
   }
  ]
);
