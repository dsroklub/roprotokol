'use strict';


// TODO Edit destinations
// TODO Edit boat categories
// TODO Edit boats
// TODO edit rower rigths
// TODO edit triptypes required rights
// TODO edit boat category required rights

app.controller('AdminCtrl', ['$scope', 'DatabaseService', 'NgTableParams', '$filter',
		function ($scope,   DatabaseService,   NgTableParams, $filter) {
		  DatabaseService.init().then(function () {
		    $scope.currentrower=null;		    
		    $scope.DB=DatabaseService.getDB;

		    $scope.getRowerByName = function (val) {
		      return DatabaseService.getRowersByNameOrId(val, undefined);
		    };
		    
     		  $scope.boatcat2dk=DatabaseService.boatcat2dk;
		  
		  }
					     )
		}
					     ]
	      )


