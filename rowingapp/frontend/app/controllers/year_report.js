'use strict';

app.controller('YearReportCtrl', ['$scope', '$rootScope', 'DatabaseService', 'NgTableParams', '$filter', '$route', '$confirm','$log',
                             function ($scope, $rootScope, DatabaseService, NgTableParams, $filter,$route,$confirm,$log) {

  $scope.statusMsg = 'Henter data...';
  $scope.statusClass = 'ok';

  DatabaseService.init().then(function () {
    DatabaseService.simpleGet('stats/year_report').then( function(response) {
      if (response.data && response.data.status === 'ok') {
        $scope.statusMsg = null;
        $scope.stats = response.data;
        $rootScope.hide_top_menu = true;
        $scope.example_year = response.data.years[ response.data.years.length - 5];
        $scope.intervals = Object.keys(response.data.rower_activity[ response.data.years[ response.data.years.length -1 ] ].intervals);
        $scope.triptypes = Object.keys(response.data.boats.triptypes);
        $scope.boattypes = Object.keys(response.data.boats.boattypes);
      } else {
        $scope.statusMsg = 'Kunne ikke hente data: ' + response.data.error;
        $scope.statusClass = 'error';
      }
    },
    function(response) {
      $scope.statusMsg = 'Kunne ikke hente data: ' + response.statusText;
      $scope.statusClass = 'error';
    })
  });

  $scope.now = new Date();

}]);
