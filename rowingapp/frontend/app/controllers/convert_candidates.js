'use strict';

app.controller('ConvertCandidatesCtrl', ['$scope', '$rootScope', 'DatabaseService', '$filter', '$confirm',
                             function ($scope, $rootScope, DatabaseService, $filter,$confirm) {

  $scope.config={'headers':{'XROWING-CLIENT':'ROPROTOKOL'}};
  DatabaseService.init().then(function () {
    DatabaseService.simpleGet('convert_candidates').then( function(response) {
      if (response.data && response.data.status === 'ok') {
        $scope.statusMsg = null;
        $scope.candidates = response.data.candidates;
        $scope.rabbits = response.data.rabbits;
        $scope.status = response.data.status;
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

  var convert_rower = function(fromrower,torower) {
    if (fromrower && torower) {
      if (fromrower != torower) {
        return DatabaseService.updateDB('convert_rower',{"from":{"id":fromrower},"to":{"id":torower}},$scope.config,$scope.errorhandler)
      } else {
        console.log("Cannot convert from " + fromrower + " to itself");
      }
    } else {
      console.log("Invalid arguments to convert_rower");
    }
  };

  $scope.convert_candidate = function(idx) {
    if ($scope.candidates[idx]) {
      var c = $scope.candidates[idx];

      convert_rower(c.rabbit_number, c.member_number)
      .then( function(res) {
        if (res.status=="ok") {
          $scope.candidates.splice(idx,1);
        } else {
          alert("Kunne ikke konvertere: " + res.error);
        }
      });
    }
  };

}]);
