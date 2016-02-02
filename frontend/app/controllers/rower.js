'use strict';
app.controller(
  'RowerCtrl',
  ['$scope', '$routeParams', 'DatabaseService', '$interval', 'ngDialog', 'ngTableParams', '$filter','$log',
   function ($scope, $routeParams, DatabaseService, $interval, ngDialog, ngTableParams, $filter,$log) {
     $scope.rowertrips=[];
     $scope.tripmembers=[];
     $scope.rowertripsaggregated=[];
     $scope.currentrower=null;
     $scope.correctedboattype=null;
     $scope.currenttrip=null;
     $scope.season=null;
     
     DatabaseService.init().then(function () {
       $scope.boatcategories = DatabaseService.getBoatTypes();
       $scope.triptypes = DatabaseService.getTripTypes();
       $scope.destinations = DatabaseService.getDestinations('DSR');                                     
     }
                                );
     $scope.DB=DatabaseService.getDB;
     
     $scope.tripselect= function(trip) {
       console.log("trip select");
       $scope.currenttrip=trip;
       DatabaseService.getTripMembers(trip.id,function (res) {
         $scope.tripmembers=res.data;
         if ($scope.correction) {
           $scope.start_correct();
         }                      
       });
     }
     
     $scope.updateRowerTrips = function(item) {
       $scope.currentrower=item;
       if ($scope.rowertrips.length>0) {
         $scope.tripselect($scope.rowertrips[0]);
       }
       
       DatabaseService.getRowerTrips($scope.currentrower,function (res) {
         if (res.data.length>0) {
           $scope.tripselect(res.data[0]);
         }
         $scope.rowertrips=res.data;
       }
                                    );
       DatabaseService.getRowerTripsAggregated($scope.currentrower,function (res) {
         $scope.rowertripsaggregated=res.data;
       }
                                              );
       $scope.mk_chart();
     }
     
     $scope.getTripMembers = function (trip) {
       return DatabaseService.getTripMembers(trip);
     }
                  
     $scope.getRowerByName = function (val) {
       return DatabaseService.getRowersByNameOrId(val, undefined);
     };
     
     $scope.closeCorrection = function (deleterequest) {
       $log.debug("close correction");
       if (deleterequest) {
         $scope.correction.deleterequest=true;
       }
       var closeCorrection=DatabaseService.closeForm('newcorrection',$scope.correction,'trip');
       closeCorrection.promise.then(function(status) {                   
         $scope.correction=null;
       })
     }
                  
     $scope.updatecorrect = function () {
       console.log("upd correct");
       $scope.correction.rowers=[];
       for (var i=0; $scope.correction.boattype && i< $scope.correction.boattype.seatcount;i++) {
         if (i< $scope.tripmembers.length) {
           $scope.correction.rowers.push($scope.tripmembers[i]);
         } else {
           $scope.correction.rowers.push(null);
         }
       }
     }
                   
     $scope.start_correct = function () {
       $scope.correction=angular.copy($scope.currenttrip);
       $scope.correction.boat=DatabaseService.getBoatWithId($scope.currenttrip.boat_id);
       $scope.correction.boattype=DatabaseService.getBoatTypeWithName($scope.correction.boat.category);
       $scope.correction.destination=DatabaseService.getDestinationWithName($scope.currenttrip.destination,$scope.correction.boat.location);
       $scope.correction.triptype=DatabaseService.getTriptypeWithID($scope.currenttrip.triptype_id,$scope.correction.boat.location);
       $scope.updatecorrect();
     };

     $scope.mk_chart = function() {
       $scope.mo={};
       if ($scope.currentrower) {
         $scope.mo.labels=[];
         $scope.mo.series=[];
         $scope.mo.data=[];
         DatabaseService.getDataNow('stats/rower_stat_month',"rower="+$scope.currentrower.id,function(d) {
           for (var wn=0;wn<53;wn++) {
             $scope.mo.labels[wn]="uge "+wn;
           }
           $scope.mo.fy=d.data[0].year;
           for (var y=$scope.mo.fy;y<=d.data[d.data.length-1].year;y++) {
             $scope.mo.data.push([]);
             $scope.mo.series.push("sæson "+y);
             for (var wn=0;wn<53;wn++) {
               $scope.mo.data[y-$scope.mo.fy][wn]=0;
             }             
           }
           angular.forEach(d.data, function(w) {
             console.log("w "+w.week+" d="+w.distance);
             $scope.mo.data[w.year-$scope.mo.fy][w.week]=w.distance/1000.0;
           },this);
         });
         console.log("got data");
       }
     }
   }
       ]
);
