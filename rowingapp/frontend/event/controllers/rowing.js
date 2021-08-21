'use strict';
angular.module('eventApp').controller(
  'rowingCtrl',
  ['$scope','$routeParams','$route','DatabaseService','LoginService','$filter','ngDialog','orderByFilter','$log','$location','$anchorScroll','$timeout',
   rowingCtrl
  ]);
function rowingCtrl ($scope, $routeParams,$route,DatabaseService, LoginService, $filter, ngDialog, orderBy, $log, $location,$anchorScroll,$timeout) {
  LoginService.check_user().promise.then(function(u) {
    $scope.current_user=u;
    $scope.rowerkm_force_email = false;
    $scope.rowerkm_include_trips = true;
    $scope.newtriptype={"active":1,"rights":[]};
    $scope.rowerkm_separate_instruction = false;
    $scope.rowerkm_only_members = false;
    $scope.rowerkm_year = new Date().getFullYear();
    $scope.newright_year = new Date().getFullYear();

      var wait_for_db = function (ok) {
    $log.debug("evt db init done");
    $scope.boatcategories=
      [{id:101,name:"Inriggere"},{id:102,name:"Coastal"},{id:103,name:"Outriggere"},{name:"Kajakker"}];
    $scope.memberrighttypes = DatabaseService.getDB('event/memberrighttypes');
    // $log.debug("events set user " + $scope.current_user);
    LoginService.set_user($scope.current_user);
  };
  DatabaseService.init({"fora":false,"file":false,"boat":true,"message":false,"event":false,"member":true,"user":true}).then(
    wait_for_db,
    function(err) {$log.debug("db init err "+err)},
    function(pg) {$log.debug("db init progress  "+pg)}
  );

    
    // $scope.current_user.is_winter_admin=null;// FIXME REMOVE
  });
}

