'use strict';

app.controller(
  'BoatCtrl',
  ['$scope', '$routeParams', 'DatabaseService', '$filter', 'ngDialog',
   function ($scope, $routeParams, DatabaseService, $filter, ngDialog) {
     $scope.allboatdamages=[];
     DatabaseService.init().then(function () {
       
       // Load Category Overview
       $scope.boatcategories = DatabaseService.getBoatTypes();
       // Load selected boats based on boat category
       $scope.allboats = DatabaseService.getBoats();
       $scope.levels =DatabaseService.getDB('boatlevels');
       $scope.brands =DatabaseService.getDB('boat_brand');      // Checkout code
       $scope.checkout_open=[];
       $scope.norower=[];
       $scope.reuse=$routeParams.reuse;
       console.log('reuse '+$scope.reuse);

       if ($scope.reuse) {
	 var reusetrip=DatabaseService.closeForm('trip/reuseopentrip',{'reusetrip':$scope.reuse},'trip');
	 reusetrip.promise.then(function(status) {
           if (status.reuse && status.reuse.id) {
             $scope.checkout.triptype=DatabaseService.getTriptypeWithID(status.reuse.triptype_id);
             $scope.checkout.destination=DatabaseService.getDestinationWithName(status.reuse.destination);
             $scope.checkout.distance=$scope.checkout.destination.distance;
             $scope.checkout.boat=DatabaseService.getBoatWithId(status.reuse.boat_id);
             $scope.selectedBoatCategory=DatabaseService.getBoatTypeWithName($scope.checkout.boat.category);
             $scope.selectedboats = DatabaseService.getBoatsWithCategoryName($scope.checkout.boat.category);
             $scope.checkout.rowers=[];
             angular.forEach(status.reuse.rowers,function(name,id,kv) {
               $scope.checkout.rowers.push(DatabaseService.getRower(id));
             }
                            );
	     // FIXME update checkout fields
	   }
	 });

       }
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
       if ($scope.triptypes.length>2)  {
         $scope.localtrip=$scope.triptypes[3];
       }
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
         'triptype': $scope.triptypes<3?null:$scope.triptypes[3],
         'rowers': ["","","","",""],
         'distance':1
       };
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
               norights.push("styrmand "+$scope.checkout.rowers[0].name+" har ikke "+ $filter('righttodk')([rq]));
             }
        }
	 } else if (subject='all') {
           for (var ri=0; ri < $scope.checkout.rowers.length; ri++) {
             if (checkout.rowers[ri] && $scope.checkout.rowers[ri].rights) {
               if (!(rq in $scope.checkout.rowers[ri].rights)) {
		 norights.push($scope.checkout.rowers[ri].name +" har ikke "+$filter('righttodk')([rq]));
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
             norights.push(" der skal være mindst een roer med "+ $filter('righttodk')([rq]));
           }
	 } else if (rq='any') {
           var ok=true;
           for (var ri=0; ri < $scope.checkout.rowers.length; ri++) {
             if (checkout.rowers[ri] && $scope.checkout.rowers[ri].rights) {
               if (!(rq in $scope.checkout.rowers[ri].rights)) {
		 ok=false;
               }
             }
           }
           if (!ok) {
             norights.push(" der må ikke være nogen "+rq+" i båden");
           }
      }	       
    }
       $scope.rightsmessage=norights.join(",");
       return norights.length<1;
     }
     
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
     
     $scope.matchBoatId = function(boat,onwater) {
       return function(matchboat) {
	 return ((boat==null || matchboat===boat) && (!!matchboat.trip==onwater) && (!$scope.selectedBoatCategory || $scope.selectedBoatCategory.name==matchboat.category));
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
    
     $scope.reportFixDamage = function (bd,reporter,damagelist,ix) {
       // reporter is an argument so that it works when calling from checkout is implementerd
       if (bd && reporter) {
	 var data={
           "damage":bd,
           "reporter":reporter
	 }
	 if (DatabaseService.fixDamage(data)) {
           damagelist.splice(ix,1);
           $scope.newdamage.reporter=null;
           $scope.allboatdamages = DatabaseService.getDamages();
           $scope.damagesnewstatus="klarmelde";
	 } else {
           $scope.damagesnewstatus="Database fejl under klarmelding";
	 }
       } else {
	 // FIXME, this does not work when calling from checkout is implementerd
	 $scope.damagesnewstatus="du skal angive, hvem du er";
       }
     };
     
     $scope.reportDamageForBoat = function (damage) {
       if (damage.degree && damage.boat && damage.description && damage.reporter) {
	 $scope.damagesnewstatus="OK";
	 var exeres=DatabaseService.updateDB_async('newdamage',damage,$scope.config).then(        
           function(data) {
             if (data.status=="ok") {
               $scope.allboatdamages.splice(0,0,data.damage);
               $scope.newdamage=null;
             }                  
           }
	 )
       } else {
	 $scope.damagesnewstatus="alle felterne skal udfyldes";
       }
     };


     $scope.dateOptions = {
       showWeeks: false
     };
  
     $scope.togglecheckout = function (tm) {   
       $scope.timeopen[tm]=!$scope.timeopen[tm];
     }

     $scope.validRowers = function () {
       
       if (!$scope.checkout.rowers || $scope.checkout.rowers.length<0) {
	 return false;
       }

       for (var i=0; i<$scope.checkout.rowers.length;i++) {
	 if (! ($scope.checkout.rowers[i] && $scope.checkout.rowers[i].name)) {
           return false;
	 }
       }
       return true;
     }
     $scope.boatcat2dk=DatabaseService.boatcat2dk;
     
     $scope.createRower = function (rowers, index,temptype) {
       var tmpnames=rowers[index].trim().split(" ");
       var last=tmpnames.splice(-1,2)[0];
       var first=tmpnames.join(" ");
       var rowerreq={
	 "firstName":first,
	 "lastName":last,
	 "type":temptype
       }
       var rower = DatabaseService.updateDB_async('createrower',rowerreq).then(
	 function(rower) {
           if (rower.error) {
             $scope.checkoutmessage=rower.error;
           } else {
             $scope.checkout.rowers[index] = rower;
           }
	 }
       );
     };  
  
     $scope.deleteopentrip = function (boat,index) {
       var data={"boat":boat};
       var closetrip=DatabaseService.closeForm('trip/deleteopentrip',data,'trip');
       closetrip.promise.then(function(status) {
         DatabaseService.reload(['boat']);
         if (status.status =='ok') {
           data.boat.trip=undefined;
           $scope.checkinmessage=status.boat+" er nu ledig, turen er slettet";
           $scope.checkin.boat=null;
         } else {
           console.log("error "+status.message);
           $scope.checkoutmessage="Fejl: "+closetrip;
         };
       }
                             )       
     }

     $scope.reusetrip = function (boat,index,km) {
       // angular bla bla send to checkout?reuse
     }

     $scope.closetrip = function (boat,index,km) {
       var data={"boat":boat};
       var closetrip=DatabaseService.closeForm('closetrip',data,'trip');
       closetrip.promise.then(function(status) {
         DatabaseService.reload(['boat']);
         if (status.status =='ok') {
           data.boat.trip=undefined;
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
       
       if ($scope.rightsmessage && $scope.rightsmessage.length>0) {
	 data.event=$scope.rightsmessage;
       }
    
       var newtrip=DatabaseService.createTrip(data);
       newtrip.promise.then(function(status) {
	 data.boat.trip=-1;
	 DatabaseService.reload(['trip']);
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
   }
       ]
);
