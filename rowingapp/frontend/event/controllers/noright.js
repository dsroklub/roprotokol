'use strict';

eventApp.controller(
  'noRight',
  ['$scope', '$routeParams', 'LoginService','$filter', 'ngDialog','orderByFilter','$log',
   function ($scope, $routeParams, LoginService, filter, ngDialog, orderBy,$log) {
     $scope.loginstatus= "";
     $scope.current_user=null;
     $scope.logout = function() {
       LoginService.logout();;
     }
     $scope.getpw = function() {
       if ($scope.login && $scope.login.member) {
         LoginService.getpw($scope.login.member);
       }
     }

     $scope.setpw = function() {
       if ($scope.login && $scope.login.pw) {
         LoginService.setpw($scope.login);
       }
     }
     
     $scope.ccurrentuser = LoginService.get_cuser();
   }
  ]
);

