'use strict';

app.controller('BoatCtrl', ['$scope', '$routeParams', 'DatabaseService', function($scope, $routeParams, DatabaseService) {
  DatabaseService.init().then(function() {
    // Load Category Overview
    $scope.destinations = DatabaseService.getDestinations();
    $scope.triptypes = DatabaseService.getTripTypes();
    $scope.boatcategories = DatabaseService.getBoatCategories();

    // Load selected boats based on boat category
    $scope.selectedboats = DatabaseService.getBoatsWithCategoryId($routeParams.boat_category);  

    // Checkout code
    $scope.selectedboat = DatabaseService.getBoatWithId($routeParams.boat_id);
    if($scope.selectedboat !== undefined) {
      var now = new Date();
      $scope.checkout = {
        'destination': $scope.destinations[0],
        'starttime' : now,
        'expectedtime': new Date(now.getTime() + 60000 * 60),
        'endtime': '',
        'triptype': $scope.triptypes[0],
        'rowers': []
      };
      // Fill the rowers array with empty values
      for(var i=0; i< $scope.selectedboat.spaces; i++) {
        $scope.checkout.rowers.push("");
      }

      $scope.boatdamages = DatabaseService.getDamagesWithBoatId($routeParams.boat_id);

    } else {
      //TODO: Say boat was not found
    }
  });

  // Utility functions for view
  $scope.getRowerByName = function (val) {
    return DatabaseService.getRowersByNameOrId(val);
  };

  $scope.clearDestination = function () {
      $scope.checkout.destination = undefined;
  };
  
}]);