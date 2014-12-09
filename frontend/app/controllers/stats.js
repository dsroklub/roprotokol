'use strict';

app.controller('StatCtrl', ['$scope', '$routeParams', 'DatabaseService', '$interval', 'ngDialog', function ($scope, $routeParams, DatabaseService, $interval, ngDialog) {
    DatabaseService.init().then(function () {      
      $scope.stats = DatabaseService.getStatistics();
      
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
}]);

