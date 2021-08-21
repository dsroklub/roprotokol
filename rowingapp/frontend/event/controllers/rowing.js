'use strict';
angular.module('eventApp').controller(
  'rowingCtrl',
  ['$scope','$routeParams','$route','DatabaseService','LoginService','$filter','ngDialog','orderByFilter','$log','$location','$anchorScroll','$timeout',
   rowingCtrl
  ]);
function rowingCtrl ($scope, $routeParams,$route,DatabaseService, LoginService, $filter, ngDialog, orderBy, $log, $location,$anchorScroll,$timeout) {
  LoginService.check_user().promise.then(function(u) {
    $scope.current_user=u;
    // $scope.current_user.is_winter_admin=null;// FIXME REMOVE
  });
}

