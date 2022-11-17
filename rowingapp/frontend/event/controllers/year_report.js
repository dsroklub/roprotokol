'use strict';
angular.module('eventApp').controller('YearReportCtrl',
                                         ['$scope', '$rootScope', 'DatabaseService', '$filter', '$route', '$confirm','$log',YearReportCtrl]);

function YearReportCtrl ($scope, $rootScope, DatabaseService,  $filter,$route,$confirm,$log) {
  $scope.statusMsg = 'Henter data...';
  $scope.statusClass = 'ok';
  $scope.now = new Date();

  DatabaseService.init().then(function () {
    DatabaseService.simpleGet('event/stats/year_report').then(
      function(response) {
        $scope.keys = Object.keys;
      if (response.data && response.data.status === 'ok') {
        $scope.statusMsg = null;
        $scope.stats = response.data;
        $rootScope.hide_top_menu = true;
        $scope.example_year = response.data.years[ response.data.years.length - 5];
        $scope.intervals = Object.keys(response.data.rower_activity[ response.data.years[ response.data.years.length -1 ] ].intervals);
        $scope.triptypes = Object.keys(response.data.boats.triptypes);
        $scope.boattypes = Object.keys(response.data.boats.boattypes);
        $scope.year_done = $scope.now.getFullYear() > response.data.parameters.to_year;
      } else {
        $scope.statusMsg = 'Kunne Ikke hente data: ' + response.data.error;
        $scope.statusClass = 'error';
      }
    },
      function(response) {
        $scope.statusMsg = 'Kunne ikke hente data: ' + response.statusText;
        $scope.statusClass = 'error';
      })
  });
}
