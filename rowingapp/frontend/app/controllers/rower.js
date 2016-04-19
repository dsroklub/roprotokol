'use strict';
app.controller(
  'RowerCtrl',
  ['$scope', '$routeParams', 'DatabaseService', '$interval', 'ngDialog', 'ngTableParams', '$filter','$log',
   function ($scope, $routeParams, DatabaseService, $interval, ngDialog, ngTableParams, $filter,$log) {
     $scope.rowertrips=[];
     $scope.datetrips=[];
     $scope.tripmembers=[];
     $scope.trip={};
     $scope.tripdate=null;
     $scope.rowertripsaggregated=[];
     $scope.rower='';
     $scope.currentrower=null;
     $scope.correctedboattype=null;
     $scope.currenttrip=null;
     $scope.season=null;
     $scope.dateOptions = {
       showWeeks: false,
       formatDay:"d",
       formatYear: 'yyyy',
       formatMonth: 'MMM',
       title:"foo"
     };

     DatabaseService.init().then(function () {
       $scope.boatcategories = DatabaseService.getBoatTypes();
       $scope.triptypes = DatabaseService.getTripTypes();
       $scope.destinations = DatabaseService.getDestinations('DSR');                                     
       if ($routeParams.rower) {
         $scope.updateRowerTrips(DatabaseService.getRower($routeParams.rower));
       }
       if ($routeParams.boat) {
         $scope.updateBoatTrips(DatabaseService.getBoatWithId($routeParams.boat));
       }
     }
                                );
     $scope.DB=DatabaseService.getDB;
     
     $scope.tripselect= function(trip) {
  //     console.log("trip select "+trip);
       $scope.currenttrip=trip;
       DatabaseService.getTripMembers(trip.id,function (res) {
         $scope.tripmembers=res.data;
         if ($scope.correction) {
           $scope.start_correct();
         }                      
       });
     }

     // Utility functions for view
     $scope.getMatchingBoats = function (vv) {
       var bts=DatabaseService.getBoats();
       var result = bts
           .filter(function(element) {
             return (element['name'].toLowerCase().indexOf(vv.toLowerCase()) == 0);
           });
       return result;
     };
     
     $scope.updateBoatTrips = function(item) {
       console.log("upd boat trips");
       $scope.correction=null;
       $scope.currenttrip=null;
       $scope.currentboat=item;
       $scope.rower = '';
       $scope.currentrower = null;       
       $scope.tripdate = null;
       
       DatabaseService.getBoatTrips($scope.currentboat,function (res) {
         if (res.data.length>0) {
           $scope.tripselect(res.data[0]);
         }
         $scope.boattrips=res.data;
       }
                                   );
       DatabaseService.getBoatTripsAggregated($scope.currentboat,function (res) {
         $scope.boattripsaggregated=res.data;
       }
                                             );
       $scope.mk_chart();
     }
     
     $scope.updateRowerTrips = function(item) {
       console.log("update rower trips");
       console.log(item);
       $scope.correction=null;
       $scope.currenttrip=null;
       $scope.currentrower=item;
       $scope.tripdate=null;
       $scope.currentboat=null;

       $scope.mates=DatabaseService.getDataNow('stats/rower','rower='+$scope.currentrower.id+"&q=mates", function (res) {
         $scope.mates=res.data;         
       }
                                              );
       
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
       } else {
         $scope.correction.deleterequest=false;
       }
       var closeCorrection=DatabaseService.closeForm('newcorrection',$scope.correction,'trip');
       closeCorrection.promise.then(function(status) {                   
         $scope.correction=null;
       })
     }
                      
     
     $scope.updatecorrect = function (boattype) {
       console.log("upd correct");
       if (boattype) {
	 $scope.correction.boat=null;
       }
       
       $scope.correction.rowers=[];
       $scope.correction.outtime=new Date( $scope.correction.outtime);
       $scope.correction.intime=new Date( $scope.correction.intime);
       for (var i=0; $scope.correction.boattype && i< $scope.correction.boattype.seatcount;i++) {
         if (i< $scope.tripmembers.length) {
	   $scope.correction.rowers.push($scope.tripmembers[i]);
         } else {
	   $scope.correction.rowers.push(null);
         }
       }
     };
       
     $scope.start_correct = function () {
       $scope.correction=angular.copy($scope.currenttrip);
       $scope.correction.boat=DatabaseService.getBoatWithId($scope.currenttrip.boat_id);
       $scope.correction.boattype=DatabaseService.getBoatTypeWithName($scope.correction.boat.category);
       $scope.correction.destination=DatabaseService.getDestinationWithName($scope.currenttrip.destination,$scope.correction.boat.location);
       $scope.correction.triptype=DatabaseService.getTriptypeWithID($scope.currenttrip.triptype_id,$scope.correction.boat.location);
       $scope.updatecorrect(false);
     };

     $scope.$watch("tripdate", function(tripdate) {
       if (tripdate) {
         $scope.correction=null;
         $scope.currenttrip=null;
         $scope.rower = "";
         $scope.currentrower = null;
         $scope.currentboat=null;
         
         DatabaseService.getDateTrips(tripdate.getFullYear()+'-'+(tripdate.getMonth()+1)+'-'+tripdate.getDate(),function (res) {
           if (res.data.length>0) {
             $scope.tripselect(res.data[0]);
           }
           $scope.datetrips=res.data;
         }
                                     );
       }
     }
		   , true);   
     
     $scope.datetrips = function() {
       alert("datetrips");
     }
     
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
           if (d.data.length>0) {
             $scope.mo.fy=d.data[0].year;
             for (var y=$scope.mo.fy;y<=d.data[d.data.length-1].year;y++) {
               $scope.mo.data.push([]);
               $scope.mo.series.push(""+y);
               for (var wn=0;wn<53;wn++) {
                 $scope.mo.data[y-$scope.mo.fy][wn]=0;
               }             
             }
             angular.forEach(d.data, function(w) {
               $scope.mo.data[w.year-$scope.mo.fy][w.week]=w.distance/1000.0;
             },this);
           }
         });
//         console.log("got data");
       }
     }
   }
  ]
);
