'use strict';

gymApp.controller(
  'teamCtrl',
  ['$scope', '$routeParams', 'DatabaseService', '$filter', 'ngDialog','$log',
   function ($scope, $routeParams, DatabaseService, $filter, ngDialog, $log) {
     DatabaseService.init().then(function () {
       $scope.teams = DatabaseService.getDB('team/team');
       $log.debug("ad  "+$scope.teams);
     });

     $scope.getRowerByName = function (val) {
       return DatabaseService.getRowersByNameOrId(val, undefined);
     };

     $scope.setTeam = function (tm) {
       $scope.currentteam=tm;
     }
       
     $scope.attend = function() {       
       var now = new Date();
       $scope.checkout = {
         'member' : $scope.attendee,
         'team' : $scope.currentteam,
         'destination': {'distance':999},
         'starttime': now,
         'comments':''
       }
       DatabaseService.attendTeam($scope.checkout);
     }
   }
  ]
);
