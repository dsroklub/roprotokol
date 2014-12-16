'use strict';

app.controller('StatCtrl', ['$scope', '$routeParams', 'DatabaseService', '$interval', 'ngDialog', 'ngTableParams', '$filter',
			    function ($scope, $routeParams, DatabaseService, $interval, ngDialog, ngTableParams, $filter) {
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
      
      
      
    });    
    $scope.isObjectAndHasId = function (val) {
      return typeof(val) === 'string' && val.length > 3;
    };
    $scope.tableParams = new ngTableParams({
      page: 1,            // show first page
      count: 500,          // count per page
      filter: {
        distance: 'asc'       // initial filter
      },
      sorting: {
        rank: 'asc'     // initial sorting
      }
    }, {
      total: function () { return DatabaseService.getStatistics().length; },
      //total: $scope.stats? $scope.stats.length:10, // length of data
      getData: function($defer, params) {
	var filteredData = DatabaseService.getStatistics();
	var orderedData = params.sorting() ?
            $filter('orderBy')(filteredData, params.orderBy()) :
            filteredData;
        $defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
      },$scope: { $data: {} }
 }
 )
}
]);

