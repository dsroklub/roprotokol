'use strict';

app.controller('BoatCtrl', ['$scope', '$routeParams', 'DatabaseService', '$interval', 'ngDialog', function ($scope, $routeParams, DatabaseService, $interval, ngDialog) {
  $scope.allboatdamages=[];
    DatabaseService.init().then(function () {

      console.log("boat DB init done");
      // Load Category Overview
      $scope.boatcategories = DatabaseService.getBoatTypes();

      // Load selected boats based on boat category
      $scope.allboats = DatabaseService.getBoats();

      // Checkout code
      var boat_id = $routeParams.boat_id;
      var destination = $routeParams.destination;
      var rowers=[];
      // TODO set defaults, for eg re-checkin
      if ($routeParams.rowers) {
	rowers= $routeParams.rowers.split(",");
      }
      $scope.checkoutmessage=null;
      $scope.timeopen={
	'start':false,
	'expected':false,
	'end':false
      };
      $scope.selectedboat = DatabaseService.getBoatWithId(boat_id);
      $scope.allboatdamages = DatabaseService.getDamages();
      $scope.triptypes = DatabaseService.getTripTypes();
      $scope.destinations = DatabaseService.getDestinations(DatabaseService.defaultLocation);
      $scope.checkoutmessage="";
      $scope.selectedBoatCategory=null;
      var now = new Date();        

      $scope.checkin = {
	'boat' : null,
      }
      
      $scope.checkout = {
	'boat' : null,
        'destination': {'distance':999},
        'starttime': now,
        // TODO: Add sunrise and sunset calculations : https://github.com/mourner/suncalc
        'expectedtime': now,
        'endtime': null, // FIXME
        'triptype': $scope.triptypes[0],
        'rowers': ["","","","",""],
	'distance':1
      };

      if ($scope.triptypes.length>2) {
	// TODO, improve hack to set default
	$scope.checkout.triptype= $scope.triptypes[2];
      }
    });

  $scope.checkRights = function() {
    tripRequirements=$scope.checkout.triptype.rights;
// HERE
  }
         // TODO: Check that all rowers has the correct right by looking at the rights table and also make sure we test if instructor
        // TODO: Show wrench next to name in checkout view

  $scope.selectBoatCategory = function(cat) {
    $scope.selectedBoatCategory=cat;
  }

  $scope.do_boat_category = function(cat) {
    $scope.selectedboats = DatabaseService.getBoatsWithCategoryName(cat.name);
    for (var i = $scope.checkout.rowers.length; i < cat.seatcount; i++) {
      $scope.checkout.rowers.push("");
    }
    $scope.checkout.rowers=$scope.checkout.rowers.splice(0,cat.seatcount);
    $scope.checkout.boat=null;
  }

  $scope.checkoutBoat = function(boat) {
    var oldboat=$scope.checkout.boat;
    $scope.checkout.boat=boat;
    $scope.destinations = DatabaseService.getDestinations(boat.location);
    $scope.boatdamages = DatabaseService.getDamagesWithBoatId(boat.id);
    if ( (!oldboat && boat.location!=DatabaseService.defaultLocation)  || (oldboat &&  oldboat.location!=boat.location)) {
      // Distance have changed, and we do not know if user overrode and accouted for location
      if ($scope.checkout.destination && $scope.checkout.destination.name)
	$scope.checkout.destination=DatabaseService.nameSearch($scope.destinations,$scope.checkout.destination.name);
    }
  }

  $scope.matchBoat = function(boat) {
    return function(matchboat) {
      return (boat==null || matchboat.boat_id==boat.id);
    }
  };

    $scope.matchBoatId = function(boat) {
    return function(matchboat) {
      return (boat==null || matchboat===boat);
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
      $scope.checkout.distance=$scope.checkout.destination.distance;
      if ($scope.checkout.starttime) {
	if($scope.checkout.triptype.name === 'Instruktion' && item.duration_instruction) {
          $scope.checkout.expectedtime = new Date($scope.checkout.starttime.getTime() + item.duration_instruction * 3600 * 1000)
	} else {
          $scope.checkout.expectedtime = new Date($scope.checkout.starttime.getTime() + item.duration * 3600 * 1000);
	}
      }
      DatabaseService.sync();
    };
  
    $scope.clearDestination = function () {
//      $scope.checkout.destination = undefined;
    };
    
  $scope.reportFixDamage = function (bd,damagelist,ix) {
    if ($scope.damages && $scope.damages.reporter && bd) {
      var data={
	"damage":bd,
	"reporter":$scope.damages.reporter
      }
      if (!DatabaseService.fixDamage(data)) {
      } else {
	damagelist.splice(ix,1);
	DatabaseService.reload();
	alert("Skade for "+bd.boat+" klarmeldt");
	$scope.damages.reporter=null;
	$scope.allboatdamages = DatabaseService.getDamages();
      }
    } else {
      $scope.damagesnewstatus="du skal angive, hvem du er";
    }
  };

  $scope.reportDamageForBoat = function () {
    if ($scope.damagedegree && $scope.selectedboat && $scope.selectedboat.id && $scope.damagedescription && $scope.damages.reporter) {
      var data={
	"degree":$scope.damagedegree,
	"boat":$scope.selectedboat,
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
	$scope.selectedboat=null;
      }
    } else {
      $scope.damagesnewstatus="alle felterne skal udfyldes";
    }
  };


  // Unused
  $scope.reportdamage = function () {
      ngDialog.open({ template: 'reportdamage.html' });
  };

  $scope.savedamage = function (boat_id, description, level) {
    var damage = { "id": 0, "descrption": description, "level": level }
    // TODO: Post to server and get id
    boatdamages.push(damage);
  };

  $scope.dateOptions = {
    showWeeks: false
  };
  
  $scope.togglecheckout = function (tm) {   
    $scope.timeopen[tm]=!$scope.timeopen[tm];
  }

  $scope.validRowers = function () {

    if (!$scope.checkout.rowers || !$scope.checkout.rowers.length>0) {
      return false;
    }

    for (var i=0; i<$scope.checkout.rowers.length;i++) {
      if (! ($scope.checkout.rowers[i] && $scope.checkout.rowers[i].name)) {
	return false;
      }
    }
    return true;
  }
  $scope.createRower = function (rowers, index) {
      var rower = DatabaseService.createRowerByName($scope.rowers[index]);
      if(rower) {
        rowers[index] = rower;
      }
    };  
  
  $scope.closetrip = function (boat) {
    var data={"boat":boat};
    var closetrip=DatabaseService.closeTrip(data);
      closetrip.promise.then(function(status) {
	DatabaseService.reload(['boat']);
	if (status.status =='ok') {
	  data.boat.trip=undefined;
	  $scope.checkoutmessage= $scope.checkout.boat.name+" er nu skrevet ind";
	  $scope.checkin.boat.trip=null;
	  $scope.checkin.boat=null;
	} else if (status.status =='error' && status.error=="not on water") {
	  $scope.checkoutmessage = $scope.checkout.boat.name + " er allerede indkrevet";
	} else {	  
	  $scope.checkoutmessage="Fejl: "+closetrip;
	};
      }
			  )
    }
  
      $scope.createtrip = function (data) {
      // TODO: Check if all rowers have ID and don't allow to start trip before it's done
      var newtrip=DatabaseService.createTrip(data);
      newtrip.promise.then(function(status) {
	data.boat.trip=-1;
	DatabaseService.reload(['boat']);
	if (status.status =='ok') {
	  $scope.checkoutmessage= $scope.checkout.boat.name+" er nu skrevet ud";
	  for (var ir=0; ir<$scope.checkout.rowers.length; ir++) {
	    $scope.checkout.rowers[ir]="";
	  }
	  $scope.checkout.boat=null;
          // TODO: clear
	} else if (status.status =='error' && status.error=="already on water") {
	  $scope.checkoutmessage = $scope.checkout.boat.name + " er allerede udskrevet, vælg en anden båd";
	} else {
	  
	  $scope.checkoutmessage="Fejl: "+JSON.stringify(newtrip);
          // TODO: give error that we could not save the trip
	};
      },function() {alert("error")}, function() {alert("notify")}  
			  )
      };

  $scope.boatSync = function (data) {
    console.log("sync for boats");
    DatabaseService.sync();
  }
  
  $scope.test = function (data) {
    DatabaseService.test('boat');
  }

  
}]);
