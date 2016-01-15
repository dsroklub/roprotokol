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
      $scope.checkoutmessage="";
      $scope.rigthsmessage="rrr";
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
    if (!$scope.checkout) {
      return false;
    }
    var tripRequirements=($scope.checkout.triptype)?$scope.checkout.triptype.rights:[];
    var boatRequirements=($scope.selectedBoatCategory)?$scope.selectedBoatCategory.rights:[];
    var reqs=DatabaseService.mergeArray(tripRequirements,boatRequirements);
    var norights=[];
    for (var rq in reqs) {
      var subject=reqs[rq];
      if (subject='cox') {
	if ($scope.checkout.rowers[0] && $scope.checkout.rowers[0].rights)  {
	  if (!(rq in $scope.checkout.rowers[0].rights)) {
	    norights.push("styrmand "+$scope.checkout.rowers[0].name+" har ikke "+rq +" ret");
	  }
	}
      } else if (subject='all') {
	for (var ri=0; ri < $scope.checkout.rowers.length; ri++) {
	  if (checkout.rowers[ri] && $scope.checkout.rowers[ri].rights) {
	    if (!(rq in $scope.checkout.rowers[ri].rights)) {
	      norights.push($scope.checkout.rowers[ri].name +" har ikke "+rq + " ret");
	    }
	  }
	}
      } else if (rq='any') {
	var ok=false;
	for (var ri=0; ri < $scope.checkout.rowers.length; ri++) {
	  if (checkout.rowers[ri] && $scope.checkout.rowers[ri].rights) {
	    if (!(rq in $scope.checkout.rowers[ri].rights)) {
	      ok=true;
	    }
	  }
	}
		      
	if (!ok) {
	  norights.push(" der skal være mindst een roer med "+rq + " ret");
	}
      }    
    }
    $scope.rightsmessage=norights.join(",");
    // HERE
    return norights.length<1;
  }
         // TODO: Check that all rowers has the correct right by looking at the rights table and also make sure we test if instructor
        // TODO: Show wrench next to name in checkout view

  $scope.selectBoatCategory = function(cat) {
    $scope.selectedBoatCategory=cat;
  }

  $scope.do_boat_category = function(cat) {
    $scope.selectedBoatCategory=cat;
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
      $scope.boatSync();
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
	  console.log("ok checkin "+status.message);	 
	  $scope.checkinmessage= status.boat+" er nu skrevet ind";
	  $scope.checkin.boat=null;
	} else if (status.status =='error' && status.error=="notonwater") {
	  $scope.checkinmessage= status.boat+" var allerede skrevet ind";
	  console.log("not on water")
	} else {
	  console.log("error "+status.message);
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
    var ds=DatabaseService.sync(['boat'])
    console.log(" boatsync ds="+ds);
    if (ds) {
      console.log(" boatsync must wait");
      ds.then(function(what) {
	console.log(" *** THEN sync boats");
	if ($scope.selectedBoatCategory) {
	  $scope.selectedboats = DatabaseService.getBoatsWithCategoryName($scope.selectedBoatCategory.name);
	  if ($scope.checkout.boat) {
	    console.log("update selected boats");
	    $scope.checkout.boat=DatabaseService.getBoatWithId($scope.checkout.boat.id);
	    if ($scope.checkout.boat.trip) {
	      console.log("selected boat was taken");
	      $scope.checkoutmessage="For sent: "+$scope.checkout.boat.name+" blev taget";
	      $scope.checkout.boat.trip=null;
	      $scope.checkout.boat=null;
	    }
	  }
	}
      });
    }
  }
  
  $scope.test = function (data) {
    DatabaseService.test('boat');
    $scope.valid=DatabaseService.valid();
  }

  $scope.valid = function () {
    DatabaseService.valid();
  }  
}]);
