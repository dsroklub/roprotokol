'use strict';

gymApp.controller(
  'teamCtrl',
  ['$scope', '$routeParams', 'DatabaseService', '$filter', 'ngDialog','$log',
   function ($scope, $routeParams, DatabaseService, $filter, ngDialog, $log) {
     $scope.attendance = [];
     DatabaseService.init({"team":true,"member":true}).then(function () {
       $scope.teams = DatabaseService.getDB('team/team');
     });
     
     DatabaseService.getDataNow("team/attendance", null, function (res) {
       $scope.attendance=res.data;         
     }
                               );
     $scope.getRowerByName = function (val) {
       return DatabaseService.getRowersByNameOrId(val, undefined);
     };

     $scope.setTeam = function (tm) {
       $scope.currentteam=tm;
     }
       
     $scope.attend = function() {
       if ($scope.currentteam) {
         $scope.checkout = {
           'member' : $scope.attendee,
           'team' : $scope.currentteam,
           'destination': {'distance':999},
           'comments':''
         }
         $scope.attendance.push( {'team': $scope.currentteam.name, membername:$scope.attendee.name, memberid:$scope.attendee.id});
         DatabaseService.attendTeam($scope.checkout);
       }
     }
   }
  ]
);
