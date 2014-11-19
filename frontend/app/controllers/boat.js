'use strict';

app.controller('BoatCtrl', ['$scope', '$routeParams', 'DatabaseService', '$interval', function ($scope, $routeParams, DatabaseService, $interval) {
    DatabaseService.init().then(function () {
      // Load Category Overview
      $scope.destinations = DatabaseService.getDestinations();
      $scope.triptypes = DatabaseService.getTripTypes();
      $scope.boatcategories = DatabaseService.getBoatCategories();

      // Load selected boats based on boat category
      $scope.selectedboats = DatabaseService.getBoatsWithCategoryName($routeParams.name);

      // Checkout code
      var boat_id = $routeParams.boat_id;
      $scope.selectedboat = DatabaseService.getBoatWithId(boat_id);
      if ($scope.selectedboat !== undefined) {
        var now = new Date();
        
        // Lock boat for the next 30 seconds
        DatabaseService.lockBoatWithId($routeParams.boat_id, new Date(now.getTime() + 30000));
        
        $scope.checkout = {
          'destination': $scope.destinations[0],
          'starttime': now,
          'expectedtime': new Date(now.getTime() + 60000 * 60),
          'endtime': '',
          'triptype': $scope.triptypes[0],
          'rowers': []
        };
        
        // Fill the rowers array with empty values
        for (var i = 0; i < $scope.selectedboat.spaces; i++) {
          $scope.checkout.rowers.push("");
        }

        $scope.boatdamages = DatabaseService.getDamagesWithBoatId(boat_id);

        var setlock = $interval(function () {
          // Lock boat for 30 seconds more
          DatabaseService.lockBoatWithId(boat_id, new Date((new Date()).getTime() + 30000));
        }, 10000);

        // Make sure we stop the timer and cancel the lock when we leave the page
        $scope.$on("$destroy", function () {
          if (setlock) {
            $interval.cancel(setlock);
            DatabaseService.lockBoatWithId(boat_id, new Date(0));
          }
        });

      } else {
        //TODO: Say boat was not found
      }

    });
    
    // Utility functions for view
    $scope.getRowerByName = function (val) {
      return DatabaseService.getRowersByNameOrId(val);
    };

    $scope.isObjectAndHasId = function (val) {
      return typeof(val) === 'string' && val.length > 3;
    };

    $scope.clearDestination = function () {
      $scope.checkout.destination = undefined;
    };
    
    $scope.createRower = function (rowers, index) {
      var rower = DatabaseService.createRowerByName(rowers[index]);
      if(rower) {
        rowers[index] = rower;
      }
    };

}]);