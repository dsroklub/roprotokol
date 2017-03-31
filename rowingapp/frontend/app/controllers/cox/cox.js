'use strict';

coxApp.controller(
  'coxCtrl',
  ['$scope', '$routeParams', 'DatabaseService', '$filter', 'ngDialog','orderByFilter','$log',
   function ($scope, $routeParams, DatabaseService, $filter, ngDialog, orderBy,$log) {
     $scope.aspirants = null;
     $scope.sortAspirants = 'team';
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
       });
       DatabaseService.getDataNow('cox/requirements',null,function (res) {
         $scope.requirements=res.data;
       });
       DatabaseService.getDataNow('cox/aspirants',null,function (res) {
         $scope.aspirants=res.data;
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

     $scope.addRequirement = function (r) {
       DatabaseService.add_cox_requirement(r).promise.then(
         function(st) {
           $scope.requirements.push(angular.copy(r));
           $log.debug("s req");
         }
       )

     }


     $scope.togglePass = function(aspirant,rq) {
       $log.debug("toggle pass"+aspirant+" :"+rq);
       if (rq.passed) {
         rq.passed=null;
         DatabaseService.add_cox_pass({aspirant:aspirant.member_id, requirement:rq.pass,pass:false}).promise.then(
           function(st) {
             $log.debug("pass revoked");
           }
         );
       } else {
         DatabaseService.add_cox_pass({aspirant:aspirant.member_id,requirement:rq.pass,pass:true}).promise.then(
         function(st) {
           $log.debug("pass updated");
         }
         );
         rq.passed=new Date().toISOString().split('T')[0];
       }
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


     $scope.sortAspirantBy = function(propertyName) {
       $scope.sortAspirants = propertyName;
       $scope.aspirants = orderBy($scope.aspirants, $scope.sortAspirants, false);
     };
     
     $scope.updateTeamForAspirant = function(aspirant) {
       DatabaseService.set_cox_team(aspirant).promise.then(
         function(st) {
           $log.debug("set team");
         }
       )
       $scope.aspirants = orderBy($scope.aspirants, $scope.sortAspirants, false);
     }
     
       $scope.addTeam = function() {
       $log.debug("add team");
       DatabaseService.add_cox_team($scope.newteam).promise.then(
         function(st) {
           var nt={"instructor_name": $scope.newteam.instructor.name, "instructor":$scope.newteam.instructor.id, "name":$scope.newteam.name};
           $scope.teams.push(nt);
         }
       );
     }
     
     $scope.dosignup = function() {
       $log.debug("sign up team");
       DatabaseService.cox_request($scope.signup).promise.then(
         function(st) {
           $log.debug("did sign up");
           $scope.signup.name=$scope.signup.aspirant.name;
           $scope.signup.activities=$scope.signup.act.join();
           $scope.aspirants.push($scope.signup);
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
