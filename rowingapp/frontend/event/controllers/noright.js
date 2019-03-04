'use strict';

angular.module('eventApp').controller(
  'noRight',
  ['$scope', '$routeParams', 'LoginService','$filter', 'ngDialog','orderByFilter','$log', noRight]);

function noRight ($scope, $routeParams, LoginService, filter, ngDialog, orderBy,$log) {
  $scope.loginstatus= "";
  $scope.current_user=null;
  $scope.gitrevision=gitrevision;
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
  
  $scope.userlogin = function(){
    LoginService.check_user().promise.then(function(u) {         
      $scope.current_user=u;
      $scope.ccurrentuser.member_id=u.member_id;
    });
  }    
}   
  
