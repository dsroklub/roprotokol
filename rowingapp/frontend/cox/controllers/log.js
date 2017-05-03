'use strict';
coxApp.controller(
  'logCtrl',
  ['$scope', '$routeParams', 'DatabaseService', '$filter', 'ngDialog','orderByFilter','$log',
   function ($scope, $routeParams, DatabaseService, $filter, ngDialog, orderBy,$log) {
     $scope.intensities = ['høj','middel','lav'];
     $scope.seasons = ['forår','sommer','efterår'];
     $scope.activities = ['INKA','POP','Gymnastik',"Coastal"];
     $scope.weekdays=["mandag","tirsdag","onsdag","torsdag","fredag","lørdag","søndag"];
     DatabaseService.init({"cox":true,"member":true,"user":true}).then(function () {
       DatabaseService.getDataNow('cox/cox_log',null,function (res) {
         $scope.coxlog=res.data;
       });
       $scope.webrower=DatabaseService.getDataNow("cox/aspirants/current_user",null,
                                                  function (res) {
                                                    $scope.webrower=res.data;
                                                  }
                                                 );       
     });     
   }
  ]
);
