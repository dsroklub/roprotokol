'use strict';

coxApp.controller(
  'noRight',
  ['$scope', '$routeParams', 'LoginService','$filter', 'ngDialog','orderByFilter','$log',
   function ($scope, $routeParams, LoginService, filter, ngDialog, orderBy,$log) {
     $scope.aspirants = null;
     $scope.sortAspirants = 'team';
     $scope.loginstatus= "";
     $scope.current_user=null;
     $scope.logout = function() {
       LoginService.logout();;
     }
     $scope.getpw = function() {
       if ($scope.login && $scope.login.aspirant) {
         LoginService.getpw($scope.login.aspirant);
       }
     }

     $scope.setpw = function() {
       if ($scope.login && $scope.login.pw) {
         LoginService.setpw($scope.login);
       }
     }
          
     $scope.ccurrentuser = LoginService.get_cuser();
   
     $scope.userlogin = function(){
       LoginService.check_user().promise.then(function(u) {         
         $scope.current_user=u;
         ccurrentuser.member_id=u.member_id;
       });
     }    
   }   
  ]
);
