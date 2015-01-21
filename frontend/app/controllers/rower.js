'use strict';
app.controller('RowerCtrl', ['$scope', '$routeParams', 'DatabaseService', '$interval', 'ngDialog', 'ngTableParams', '$filter',
			     function ($scope, $routeParams, DatabaseService, $interval, ngDialog, ngTableParams, $filter) {
			       $scope.rowertrips=[];
			       $scope.currentrower;
			       DatabaseService.init().then(function () {
			       }
							  );
			       
			       $scope.updateRowerTrips = function(item) {
				 $scope.currentrower=item;
				 DatabaseService.getRowerTrips($scope.currentrower,function (res) {
				   $scope.rowertrips=res.data;
				   }
				 );
			       }
			       
			       $scope.getRowerByName = function (val) {
				 return DatabaseService.getRowersByNameOrId(val, undefined);
			       };
			     }
			    ]
	      );
