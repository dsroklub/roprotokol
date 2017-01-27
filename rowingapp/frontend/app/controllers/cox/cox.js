'use strict';

coxApp.controller(
  'coxCtrl',
  ['$scope', '$routeParams', 'DatabaseService', '$filter', 'ngDialog','$log',
   function ($scope, $routeParams, DatabaseService, $filter, ngDialog, $log) {
     $scope.aspirant = null;
     $scope.teams=[];
     $scope.todpattern="[0-2]\\d:[0-5]\\d";
     $scope.intensities = ['høj','middel','lav'];
     $scope.seasons = ['forår','sommer','efterår'];
     $scope.activities = ['INKA','POP','Gymnastik',"Coastal"];
     $scope.signup={act:[]};
     $scope.weekdays=["mandag","tirsdag","onsdag","torsdag","fredag","lørdag","søndag"];
     DatabaseService.init({"cox":true,"member":true}).then(function () {
       DatabaseService.getDataNow('cox/team',null,function (res) {
         $scope.teams=res.data;
       }
                                                );
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
       
     $scope.notCox = function() {
       return function(rower) {
         for (var ri in rower.rights) {
           if (rower.rights[ri].member_right=="cox") {
             return false;
           }
         }
         return true;
       }
     }

     $scope.coxinstructor = function() {
       return function(rower) {
         for (var ri in rower.rights) {
           if (rower.rights[ri].member_right=="instructor" && rower.rights[ri].arg=="cox") {
             return true;
           }
         }
         return false;
       }
     }

     $scope.addTeam = function() {
       $log.debug("add team");
       DatabaseService.add_cox_team($scope.newteam).promise.then(
         function(st) {
           $scope.newteam.instructor=$scope.newteam.instructor.name;
           $scope.teams.push($scope.newteam);
         }
       );
     }
     
     $scope.dosignup = function() {
       $log.debug("sign up team");
       DatabaseService.cox_signup($scope.signup).promise.then(
         function(st) {
           $log.debug("did sign up");
         }
       );
     }

     $scope.deleteTeam = function(tm) {
       $log.debug("delete team");
       DatabaseService.deleteCoxTeam(tm).promise.then(
         function(st) {
           $scope.teams.splice($scope.teams.indexOf(tm),1);
         }
       );
     }


   }
  ]
);
