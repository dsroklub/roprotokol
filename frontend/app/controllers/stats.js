'use strict';

var rabbitComperator = function(mid) {
  return (mid.length >0 && mid[0]=='k');
};

app.controller('StatCtrl', ['$scope', 'DatabaseService', 'NgTableParams', '$filter',
		function ($scope,   DatabaseService,   NgTableParams, $filter) {
    DatabaseService.init().then(function () {
      
      // (Need membership Start date, End Date for following information)
      
      // TODO: Add gray wrench when rower has more then 100KM for the year until last sunday in april next year when it turns red if we have not registred any work
      // 
      
      // TODO: Stacked Barchart - Membership turnover (Filter on Date)
      // Membership turnover per year/month, two bars per month/year joined and left the club stacked by sex
      
      // TODO: Stacked Barchart - Members per year/month (Filter on Date)
      // Members per year/month, two bars per month/year joined and left the club stacked by sex

      // TODO: Stacked Barchart - Seniority (Filter on Date)
      // Seniority, bar per year stacked by sex
      
      // TODO: Age Barchart - Seniority (Filter on Date)
      // Age, bar per year stacked by sex
      
      // TODO: Stacked Barchart - Rowed KM (Filter on Date)
      // Rowed KM, two bars rowboat and kajak stacked by sex
      
      // TODO: Barchart - Triptype - Trips (Filter on Date)
      // Number of trips per Triptype
      
      // TODO: Barchart - Triptype - KM (Filter on Date)
      // Number of KM per Triptype
      
      // TODO: Barchart - Triptype - Person trips (Filter on Date)
      // Number of person trips per Triptype
      
      // TODO: Stacked Barchart - Member activiy KM per intervals per year in % (Filter on Date)
      // Member activiy KM per intervals <100, 100-200, 200-300, 300-500, >500
      
      // TODO: Stacked Barchart - Instructions per finish rabbit
      
      // TODO: Who rows with most different people
      
      // TODO: Add more from page 11
      
      
      
     $scope.boattype="any";
     $scope.docats = function (val) {
       $scope.rowcategory=val;
	if (val=='kaniner') {
	  $scope.tableParams.filter({'id':'k'});
	  $scope.boattype='any';
	} else {
	  $scope.tableParams.filter({'id':''});
	  $scope.boattype=val;
	  $scope.tableParams.reload();
	  $scope.boattableParams.reload();
	}
    };
			      
      $scope.isObjectAndHasId = function (val) {
      return typeof(val) === 'string' && val.length > 3;
    };

   $scope.tableParams = new NgTableParams({
     page: 1,            // show first page
     count: 500,          // count per page
     filter: {
       id: ''       // initial filter	
     },
     sorting: {
       rank: 'asc'     // initial sorting
     }
   }, {
     getData: $scope.getRowerData
   });

   $scope.boattableParams = new NgTableParams({
     page: 1,            // show first page
     count: 500,          // count per page
     filter: {
       boatname: ''       // initial filter	
     },
     sorting: {
       rank: 'asc',     // initial sorting
     }
   }, {
     getData: $scope.getBoatData
   });      
    }
      );		  
       $scope.getRowerData = function getRowerData(params) {
	var filterInfo = params.filter();
	var rawData = DatabaseService.getRowerStatistics($scope.boattype);
	var filteredData=filterInfo ? $filter('filter')(rawData, filterInfo) : rawData;	
	var orderedData = params.sorting() ?
	    $filter('orderBy')(filteredData, params.orderBy()) :
	    filteredData;
	if (orderedData) {
	  orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count());
	}
	return orderedData;
      }

      $scope.getBoatData = function getBoatData(params) {
	 var filterInfo = params.filter();
	 var rawData = DatabaseService.getBoatStatistics($scope.boattype);
	 var filteredData=filterInfo ? $filter('filter')(rawData, filterInfo) : rawData;	
	 var orderedData = params.sorting() ?
	     $filter('orderBy')(filteredData, params.orderBy()) :
	     filteredData;
	 if (orderedData) {
	   orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count());
	 }
	 return orderedData;
	}

		  
     		  $scope.boatcat2dk=DatabaseService.boatcat2dk;
		  
		}
			   ]
     )

