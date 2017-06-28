'use strict';
coxApp.controller(
  'logCtrl',
  ['$scope', '$routeParams', 'DatabaseService', '$filter', 'ngDialog','orderByFilter','$log','$timeout',
   function ($scope, $routeParams, DatabaseService, $filter, ngDialog, orderBy,$log, $timeout) {
     $scope.dbready=false;
     $scope.dbgrace=true;

     $scope.intensities = ['høj','middel','lav'];
     $scope.seasons = ['forår','sommer','efterår'];
     $scope.activities = ['INKA','POP','Gymnastik',"Coastal"];
     $scope.weekdays=["mandag","tirsdag","onsdag","torsdag","fredag","lørdag","søndag"];
     $timeout(function() { $scope.dbgrace = false;}, 2000);

     DatabaseService.init({"cox":true,"member":true,"user":true}).then(function () {
       DatabaseService.getDataNow('cox/aspirants/team',null,function (res) {
         $scope.teams=res.data;
       });

       DatabaseService.getDataNow('cox/cox_log',null,function (res) {
         $scope.dbready=true;
         $scope.coxlog=res.data;
       });
       $scope.webrower=DatabaseService.getDataNow("cox/aspirants/current_user",null,
                                                  function (res) {
                                                    $scope.webrower=res.data;
                                                  }
                                                 );       
     });

     $scope.set_team = function(teamName) {
       DatabaseService.getDataNow('cox/aspirants/team_activity',"team="+encodeURIComponent(teamName),function (res) {
         $scope.teamtrips = res.data;
       });
     }

     
   }
  ]
);
