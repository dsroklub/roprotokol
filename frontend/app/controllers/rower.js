'use strict';
app.controller('RowerCtrl', ['$scope', '$routeParams', 'DatabaseService', '$interval', 'ngDialog', 'ngTableParams', '$filter',
			     function ($scope, $routeParams, DatabaseService, $interval, ngDialog, ngTableParams, $filter) {
			       $scope.rowertrips=[];
			       $scope.tripmembers=[];
			       $scope.rowertripsaggregated=[];
			       $scope.currentrower=null;
			       $scope.currenttrip=null;
			       DatabaseService.init().then(function () {
			       }
							  );
			       $scope.tripselect= function(trip) {
				 $scope.currenttrip=trip;
				 DatabaseService.getTripMembers(trip.id,function (res) {
				   $scope.tripmembers=res.data;
				 });
			       }
			       
			       $scope.updateRowerTrips = function(item) {
				 $scope.currentrower=item;
				 DatabaseService.getRowerTrips($scope.currentrower,function (res) {
				   $scope.rowertrips=res.data;
				   }
				 );
				 DatabaseService.getRowerTripsAggregated($scope.currentrower,function (res) {
				   $scope.rowertripsaggregated=res.data;
				   }
				 );
			       }

			       $scope.getTripMembers = function (trip) {
				 return DatabaseService.getTripMembers(trip);
			       }

			       $scope.getRowerByName = function (val) {
				 return DatabaseService.getRowersByNameOrId(val, undefined);
			       };
			     }
			    ]
	      );
