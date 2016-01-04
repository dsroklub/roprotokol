'use strict';

app.controller('BoatCtrl', ['$scope', '$routeParams', 'DatabaseService', '$interval', 'ngDialog', function ($scope, $routeParams, DatabaseService, $interval, ngDialog) {
  $scope.allboatdamages=[];
    DatabaseService.init().then(function () {
      
      // Load Category Overview
      $scope.boatcategories = DatabaseService.getBoatCategories();

      // Load selected boats based on boat category
      $scope.selectedboats = DatabaseService.getBoatsWithCategoryName($routeParams.name);
      $scope.allboats = DatabaseService.getBoats();

      // Checkout code
      var boat_id = $routeParams.boat_id;
      $scope.selectedboat = DatabaseService.getBoatWithId(boat_id);
      $scope.allboatdamages = DatabaseService.getDamages();
      if ($scope.selectedboat !== undefined) {
        var now = new Date();
        
        $scope.destinations = DatabaseService.getDestinations($scope.selectedboat.location);
        $scope.triptypes = DatabaseService.getTripTypes();
        
        // Lock boat for the next 30 seconds
        DatabaseService.lockBoatWithId($routeParams.boat_id, new Date(now.getTime() + 30000), function() {
          // Failed to lock boat
          // TODO: Give error and redirect back to category list
        });
        
        // Create initial data for checkout
        $scope.checkout = {
	  'boat' : $scope.selectedboat,
          'destination': {'distance':999},
          'starttime': now,
          // TODO: Add sunrise and sunset calculations : https://github.com/mourner/suncalc
          'expectedtime': now,
          'endtime': '',
          'triptype': $scope.triptypes[0],
          'rowers': []
        };
        // debugger;
        
        // TODO: Check that all rowers has the correct right by looking at the rights table and also make sure we test if instructor
        // TODO: Show wrench next to name in checkout view
        // TODO: Don't default on triptype and block checkout
        // TODO: Make sure we calculate from boat placement
        
        // Fill the rowers array with empty values
        for (var i = 0; i < $scope.selectedboat.spaces; i++) {
          $scope.checkout.rowers.push("");
        }

	$scope.boatdamages = DatabaseService.getDamagesWithBoatId(boat_id);

        var setlock = $interval(function () {
          // Lock boat for 30 seconds more every 10 seconds
          DatabaseService.lockBoatWithId(boat_id, new Date((new Date()).getTime() + 30000));
        }, 10000);

        // Make sure we stop the timer and cancel the lock when we leave the page
        $scope.$on("$destroy", function () {
          if (setlock) {
            $interval.cancel(setlock);
            // Unlock boat
            DatabaseService.lockBoatWithId(boat_id, new Date(0));
          }
        });

      } else {
        //TODO: Say boat was not found
      }

    });


  $scope.matchBoat = function(boat) {
    return function(damage) {
      return (boat==null || damage.boat_id==boat.id);
    }
  };

  // Utility functions for view
  $scope.getMatchingBoats = function (vv) {
    var bts=DatabaseService.getBoats();
    var result = bts
	.filter(function(element) {
	  return (element['name'].toLowerCase().indexOf(vv.toLowerCase()) == 0);
	});
    return result;

  };

  $scope.getRowerByName = function (val) {
    // Generate list of ids that we already have added
    return DatabaseService.getRowersByNameOrId(val);
  }
  
  $scope.getRowersByName = function (val) {
    // Generate list of ids that we already have added
    var ids = {};
    for(var i = 0; i < $scope.checkout.rowers.length; i++) {
        if(typeof($scope.checkout.rowers[i]) === 'object') {
          ids[$scope.checkout.rowers[i].id] = true;
        }
      }
      return DatabaseService.getRowersByNameOrId(val, ids);
    };

    $scope.isObjectAndHasId = function (val) {
      return typeof(val) === 'string' && val.length > 3;
    };

    $scope.updateCheckout = function (item) {
      // Calculate expected time based on triptype and destination
      $scope.checkout.destination=item;
      if($scope.checkout.triptype.name === 'Instruktion' && item.duration_instruction) {
        $scope.checkout.expectedtime = new Date($scope.checkout.starttime.getTime() + item.duration_instruction * 3600 * 1000)
      } else {
        $scope.checkout.expectedtime = new Date($scope.checkout.starttime.getTime() + item.duration * 3600 * 1000);
      }
    };
  
    $scope.clearDestination = function () {
//      $scope.checkout.destination = undefined;
    };
    
  $scope.reportFixDamage = function (did) {
      alert("Damage "+did+" fixed");
    };

  $scope.reportDamageForBoat = function () {
    if ($scope.damagedegree && $scope.damagedboat && $scope.damagedboat.id && $scope.damagedescription && $scope.damages.reporter) {
      var data={
	"degree":$scope.damagedegree,
	"boat":$scope.damagedboat,
	"description":$scope.damagedescription,
	"reporter":$scope.damages.reporter
      }
      $scope.damagesnewstatus="OK";
      alert("Damage "+JSON.stringify(data));
      if (!DatabaseService.newDamage(data)) {
	alert("new damage failed");
      } else {
	$scope.damagedegree=null;
	$scope.damages.reporter=null;
	$scope.damagedescription=null;
	$scope.damagedboat=null;
      }
    } else {
      alert("alle felterne skal udfyldes");
      $scope.damagesnewstatus="alle felterne skal udfyldes";
    }
  };

  $scope.reportdamage = function () {
      ngDialog.open({ template: 'reportdamage.html' });
    };

    $scope.savedamage = function (boat_id, description, level) {
      var damage = { "id": 0, "descrption": description, "level": level }
      alert('save damage '+id+' '+description+' level'+level);
      // TODO: Post to server and get id
      boatdamages.push(damage);
    };
  
    $scope.createRower = function (rowers, index) {
      var rower = DatabaseService.createRowerByName(rowers[index]);
      if(rower) {
        rowers[index] = rower;
      }
    };  
  
    $scope.createtrip = function (data) {
      // TODO: Check if all rowers have ID and don't allow to start trip before it's done      
      if(DatabaseService.createTrip(data)) {
        // TODO: redirect to category list
      } else {
        // TODO: give error that we could not save the trip
      };
    };
  
}]);
