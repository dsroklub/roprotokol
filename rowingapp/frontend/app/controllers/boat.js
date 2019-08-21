'use strict';
// cico==1 checkin
// cico=2 checkout

angular.module('rowApp').controller(
  'BoatCtrl',
  ['$scope', '$routeParams', 'DatabaseService', '$filter', 'ngDialog','$log','$location',
   BoatCtrl
  ]);

function BoatCtrl ($scope, $routeParams, DatabaseService, $filter, ngDialog,$log, $location) {
  $scope.allboatdamages=[];
  $scope.destinations=[];
  $scope.burl=$location.$$absUrl.split("ind/")[0];
  $scope.isName = function(n) {
    if (!n) {
      return false;
    }
    if (n.length>3 && isNaN(n)) {
      return true;
    }
    return false;
  };

  $scope.reservation_current = function() {
    return function(reservation) {
      return (reservation.dayofweek<1 || reservation.configuration==$scope.status.reservation_configuration);
    }
  };

  $scope.newdamage={};
  $scope.status={};
  $scope.boat_type=null;
  $scope.boatcategories=[];
  DatabaseService.init({"status":true,"stats":false, "boat":true, "member":true, "trip":true, "reservation":true}).then(function () {
    // Load Category Overview
    var reservations=DatabaseService.getDB('get_reservations');
    $scope.newdamage.reporter=DatabaseService.getCurrentRower();
    $scope.status.reservation_configuration=DatabaseService.getDB('status').reservation_configuration;
    $scope.boatcategories = DatabaseService.getBoatTypes();
    $scope.nowtime=new Date();
    var endofday=new Date();
    $scope.twohoursec=((2+$scope.nowtime.getHours())*60+$scope.nowtime.getMinutes())*60+$scope.nowtime.getSeconds();
    $scope.sculler_open=DatabaseService.getDB('status').sculler_open;
    // Load selected boats based on boat category
    $scope.reservations = DatabaseService.getDB('get_reservations');
    $scope.onwater = DatabaseService.getDB('onwater');
    $scope.boat_notes = DatabaseService.getDB('boat_notes');
    $scope.checkin={update_destination_for:null};
    $scope.critical_time = function (tx) {
      if (tx) {
        var t=tx.split(/[- :]/);
        var et=new Date(t[0], t[1]-1, t[2], t[3]||0, t[4]||0, t[5]||0);
        return(et< new Date);
      }
      return false;
    };
    // FIXME also in admin, antiduplicate
    $scope.getTriptypeWithID=DatabaseService.getTriptypeWithID;
    $scope.weekdays=[
      {id:0,day:"-"},
      {id:1,day:"mandag"},
      {id:2,day:"tirsdag"},
      {id:3,day:"onsdag"},
      {id:4,day:"torsdag"},
      {id:5,day:"fredag"},
      {id:6,day:"lørdag"},
      {id:7,day:"søndag"}
    ];

    DatabaseService.update_reservations();
    $scope.allboats = DatabaseService.getBoats();
    $scope.levels =DatabaseService.getDB('boatlevels');
    $scope.coxteams =DatabaseService.getDB('coxteams');
    $scope.brands =DatabaseService.getDB('boat_brand');      // Checkout code
    $scope.checkout_open=[];
    $scope.norower=[];
    $scope.reuse=$routeParams.reuse;
    $scope.checkoutmessage="";
    $scope.checkoutnotification="";
    $scope.checkouterrormessage="";
    $scope.rigthsmessage="rrr";
    $scope.timeopen={
      'start':false,
      'expected':false,
      'end':false
    };

    var now = new Date();
    $scope.checkout = {
      'boat' : null,
      'destination': {'distance':999,'duration':null},
      'starttime': now,
      'expectedtime': now,
      'endtime': null,
      'triptype': null,
      'rowers': ["","","","",""],
      'client_name':DatabaseService.client_name(),
      'distance':0,
      'comments':''
    };
    $scope.starttime_clean= now;
    $scope.destinations = DatabaseService.getDestinations(DatabaseService.defaultLocation);
    if ($scope.reuse) {
      var reusetrip=DatabaseService.closeForm('trip/reuseopentrip',{'reusetrip':$scope.reuse},'trip');
      reusetrip.promise.then(function(reuse) {
        if (reuse.id) {
          $scope.checkout.triptype=DatabaseService.getTriptypeWithID(reuse.triptype_id);
          $scope.checkout.destination=$scope.get_destination(reuse.destination);
          $scope.checkoutForm.checkout_destination.$setDirty();
          $scope.checkout.distance=$scope.checkout.destination.distance;
          $scope.checkout.boat=DatabaseService.getBoatWithId(reuse.boat_id);
          $scope.checkout.comments=reuse.comment;
          $scope.checkout.starttime=new Date(reuse.outtime);
          $scope.checkout.expectedtime=new Date(reuse.expectedintime);
          $scope.checkout.expectedtime_dirty=1;
          $scope.selectedBoatCategory=DatabaseService.getBoatTypeWithName($scope.checkout.boat.category);
          $scope.selectedboats = DatabaseService.getBoatsWithCategoryName($scope.checkout.boat.category);
          $scope.checkout.rowers=[];
          angular.forEach(reuse.rowers,function(kv) {
            $scope.checkout.rowers.push(DatabaseService.getRower(kv.member_id));
          }
                         );
          // $scope.updateExpectedTime();
        }
      });
    }
    var boat_id = $routeParams.boat_id;
    var destination = $routeParams.destination;
    if (boat_id) {
      $scope.selectedboat = DatabaseService.getBoatWithId(boat_id);
    }
    $scope.allboatdamages = DatabaseService.getDamages();
    $scope.triptypes = DatabaseService.getTripTypes();
    $scope.checkoutmessage="";
    $scope.checkoutnotification="";
    $scope.usersettime=false;
    $scope.selectedBoatCategory=null;
    $scope.checkin = {
      'boat' : null,
    }
    if ($scope.cico==2) {
      $scope.do_boat_category(DatabaseService.lookup('boattypes','name','Inrigger 4+'));
    }
  });
  var has_right = function(required_right,arg,rightlist) {
    for (var ri=0; ri<rightlist.length; ri++) {
      // DSR Hack here
      if (
        (rightlist[ri].member_right==required_right || (required_right=="svava" && rightlist[ri].member_right=="sculler"))
          && (!arg || !rightlist[ri].arg || arg==rightlist[ri].arg)
      ) {
        if (!$scope.sculler_open || rightlist[ri].arg!='sommer') {
          return true;
        }
      }
    }
    return false;
  }

  $scope.get_destination = function(destination) {
    for (var di=0; di<$scope.destinations.length; di++) {
      if ($scope.destinations[di].name == destination) {
        return($scope.destinations[di]);
      }
    }
    return null;
  }
  $scope.checkRights = function() {
    if (!$scope.checkout) {
      return false;
    }
    var tripRequirements=($scope.checkout.triptype)?$scope.checkout.triptype.rights:[];
    var boatRequirements=($scope.selectedBoatCategory)?$scope.selectedBoatCategory.rights:[];
    var reqs=tripRequirements.concat(boatRequirements);
    var norights=[];
    var subright=null;
    if ($scope.selectedBoatCategory) {
      subright=$scope.selectedBoatCategory.rights_subtype;
    }
    angular.forEach(reqs, function(r,k) {
      var rq=r.required_right;
      var subject=r.requirement;
      var arg=rq=='instructor'?subright:null;
      if (rq == null) {
        // Skip, we are waiting for Mysql Json AGG in MariaDB 10.5
      } else if (subject=='cox') {
        if ($scope.checkout.rowers[0] && $scope.checkout.rowers[0].rights)  {
          if (!(has_right(rq,arg,$scope.checkout.rowers[0].rights))) {
            this.push("styrmand "+$scope.checkout.rowers[0].name+" Har ikke "+ $filter('righttodk')([rq]));
          }
        }
      } else if (subject=='all') {
        for (var ri=0; ri < $scope.checkout.rowers.length; ri++) {
          if ($scope.checkout.rowers[ri] && $scope.checkout.rowers[ri].rights) {
            if (!($scope.checkout.rowers[ri].id.charAt(0)=='g')) { // we do not know the rights of guests
                if (!(has_right(rq,arg,$scope.checkout.rowers[ri].rights))) {
                  this.push($scope.checkout.rowers[ri].name +" har ikke "+$filter('righttodk')([rq]));
              }
            }
          }
        }
      } else if (subject=='any') {
        var anyok=false;
        for (var ri=0; ri < $scope.checkout.rowers.length; ri++) {
          if ($scope.checkout.rowers[ri] && $scope.checkout.rowers[ri].rights) {
            if ((has_right(rq,arg,$scope.checkout.rowers[ri].rights))) {
              anyok=true;
            }
          }
        }
        if (!anyok) {
          this.push(" der skal være mindst een roer med "+ $filter('righttodk')([rq]));
        }
      } else if (subject=='none') {
        var noneok=true;
        for (var ri=0; ri < $scope.checkout.rowers.length; ri++) {
          if ($scope.checkout.rowers[ri] && $scope.checkout.rowers[ri].rights) {
            if (has_right(rq,arg,$scope.checkout.rowers[ri].rights)) {
              this.push($scope.checkout.rowers[ri].name +" "+$filter('righttodk')([rq]));
              noneok=false;
            }
          }
        }
        if (!noneok) {
          this.push(" der må ikke være nogen "+$filter('righttodk')([rq])+" i båden");
        }
      }
    },norights);
    // Check reservation
    // WIP, works for daytrips
    angular.forEach($scope.reservations, function(rv) {
      var otime=$scope.checkout.starttime;
      var etime=$scope.checkout.expectedtime;
      var pp_timediff=function(t1,t2) {
        if (t1.getFullYear() === t2.getFullYear() && t1.getMonth() === t2.getMonth() && t1.getDate() === t2.getDate()) {
          return (t1.toLocaleDateString("da-DK",{"hour":"numeric","minute":"numeric"}) + " til " +
                  t2.toLocaleDateString("da-DK",{"hour":"numeric","minute":"numeric"}));
        } else {
          return (t1.toLocaleDateString("da-DK",{"day":"numeric","month":"numeric","year":"numeric","hour":"numeric","minute":"numeric"})+  " til " +
                  t2.toLocaleDateString("da-DK",{"day":"numeric","month":"numeric","year":"numeric","hour":"numeric","minute":"numeric"}));
        }
      }
      if ($scope.checkout.triptype && $scope.checkout.boat && $scope.checkout.boat.id==rv.boat_id && etime) {
        if (rv.dayofweek>0) {
          // Ugereservering
          if (etime.getDay()==(rv.dayofweek)) {
            var st=angular.copy(etime);
            var et=angular.copy(etime);
            st.setHours(rv.start_time.split(":")[0]);
            st.setMinutes(rv.start_time.split(":")[1]);
            st.setSeconds(0);
            et.setHours(rv.end_time.split(":")[0]);
            et.setMinutes(rv.end_time.split(":")[1]);
            et.setSeconds(0);
            if (!(
              rv.triptype_id==$scope.checkout.triptype.id ||
                (etime < st && otime < st) ||
                (etime > et && otime > et)
            )
               ) {
              this.push(rv.boat+" er reserveret til "+ DatabaseService.getTriptypeWithID(rv.triptype_id).name + " "+
                        (rv.purpose==DatabaseService.getTriptypeWithID(rv.triptype_id).name?"": rv.purpose+" ")+ rv.start_time+" til "+rv.end_time);
            }
          }
        } else {
          // kalendereservering
          var st=new Date(rv.start_date + "T"+ rv.start_time);
          var et=new Date(rv.end_date + "T"+ rv.end_time);
          if (!(
            rv.triptype_id==$scope.checkout.triptype.id ||
              (etime < st && otime < st) ||
              (etime > et && otime > et)
          )
             )
          {
            this.push(rv.boat+" er reserveret til "+ DatabaseService.getTriptypeWithID(rv.triptype_id).name + " :"+rv.purpose+
                         " "+ pp_timediff(st,et));
          }
        }
      }
    },norights);
    if ($scope.checkout.boat && $scope.checkout.boat.damage > 2) {
      norights.push(" Båden er svært skadet og må derfor ikke komme på vandet !!!");
    }

    $scope.rightsmessage=norights.join(", ");
    return norights.length<1;
  }
  $scope.selectBoatCategory = function(cat) {
    $scope.selectedBoatCategory=cat;
  }

  $scope.do_boat_category = function(cat) {
    $scope.selectedBoatCategory=cat;
    DatabaseService.update_reservations();
    $scope.checkoutmessage=null;
    $scope.checkoutnotification=null;
    $scope.selectedboats = DatabaseService.getBoatsWithCategoryName(cat.name);
    for (var i = $scope.checkout.rowers.length; i < cat.seatcount; i++) {
      $scope.checkout.rowers.push("");
    }
    $scope.checkout.rowers=$scope.checkout.rowers.splice(0,cat.seatcount);
    $scope.checkout.boat=null;
  }
  $scope.checkoutBoat = function(boat) {
    var oldboat=$scope.checkout.boat;
    $scope.checkoutmessage=null;
    $scope.checkoutnotification=null;
    $scope.checkout.boat=boat;
    $scope.destinations = DatabaseService.getDestinations(boat.location);
    $scope.boatdamages = DatabaseService.getDamagesWithBoatId(boat.id);
    if ($scope.destinations && $scope.destinations.length==1) {
      $scope.checkout.destination=$scope.destinations[0];
      if (!$scope.checkout.destination.duration) {
        $scope.checkout.starttime=null;
        $scope.checkout.expectedtime=null;
      }
    } else {
      var now = new Date();
      if (!$scope.checkout.starttime || $scope.checkout.starttime<now) {
        $scope.checkout.starttime=now;
        $scope.starttime_clean= now;
      }
      if ( (!oldboat && boat.location!=DatabaseService.defaultLocation)  || (oldboat &&  oldboat.location!=boat.location)) {
      // Distance have changed, and we do not know if user overrode and accounted for location
      if ($scope.checkout.destination && $scope.checkout.destination.name)
        $scope.checkout.destination=DatabaseService.nameSearch($scope.destinations,$scope.checkout.destination.name);
      }
    }
  }

  $scope.matchBoat = function(boat) {
    return function(matchboat) {
      return (matchboat.id && (boat==null || matchboat.boat_id==boat.id));
    }
  };

  $scope.matchType = function(boat,boat_type) {
    return function(matchboat) {
      return (matchboat.boat_type && (!boat_type || matchboat.boat_type==boat_type.name));
    }
  };

  $scope.matchBoatAndType = function(boat,boat_type) {
    return function(matchboat) {
      return (matchboat.id && (boat==null || matchboat.boat_id==boat.id) && (!boat_type || matchboat.boat_type==boat_type.name));
    }
  };
  $scope.matchBoatId = function(boat,onwater) {
    return function(matchboat) {
      return ((!boat || matchboat===boat) && ((!onwater && (!matchboat.trip || matchboat.location=='Andre' || ($scope.checkout.expectedtime && new Date(matchboat.outtime) > $scope.checkout.expectedtime) )) || (onwater && matchboat.trip)) &&
              (!$scope.selectedBoatCategory || $scope.selectedBoatCategory.name==matchboat.category) && matchboat.location);
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

  $scope.getMatchingBoatsWithType = function (vv,boat_type) {
    var bts=DatabaseService.getBoats();
    var result = bts
        .filter(function(boat) {
          return ( boat['name'].toLowerCase().indexOf(vv.toLowerCase()) == 0  && (!boat_type || boat_type.name==boat.category));
        });
    return result;
  };

  $scope.getRowerByName = function (val) {
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
    $scope.boatSync();
  };
  $scope.updateExpectedTime = function () {
    if ($scope.checkout.starttime && $scope.checkout.destination) {
      var duration=($scope.checkout.triptype && $scope.checkout.triptype.name === 'Instruktion' && $scope.checkout.destination.duration_instruction)?
          $scope.checkout.destination.duration_instruction:
          $scope.checkout.destination.duration;
      if (duration>0) {
        $scope.checkout.expectedtime = new Date($scope.checkout.starttime.getTime() + duration * 3600 * 1000);
      } else {
        $scope.checkout.expectedtime = null;
      }
    }
  }

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
        damagelist.splice(damagelist.indexOf(bd),1);
        $scope.newdamage.reporter=DatabaseService.getCurrentRower();
        $scope.allboatdamages = DatabaseService.getDamages();
        $scope.damagesnewstatus="klarmeldt";
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
            $scope.newdamage={};
            $scope.newdamage.reporter=DatabaseService.getCurrentRower();
          }
        }
      )
    } else {
      $scope.damagesnewstatus="alle felterne skal udfyldes";
    }
  };
  $scope.min_time=new Date();
  $scope.dateOptions = {
    showWeeks: false,
  };
  $scope.expectedOptions = {
    showWeeks: false,
  };

  $scope.set_expected = function () {
    if ($scope.checkout.expectedtime) {
      $scope.checkout.expectedtime_dirty=1;
    }
  }

  $scope.newStartTime = function () {
    if ($scope.checkout && $scope.checkout.starttime && ($scope.checkout.exptectedtime < $scope.checkout.starttime|| !$scope.checkout.expectedtime_dirty)) {
      if ($scope.checkout.destination) {
        if ($scope.checkout.destination.duration) {
          var tdiff=$scope.checkout.destination.duration*3600000;
          $scope.checkout.expectedtime=new Date($scope.checkout.starttime.getTime()+tdiff);
        } else if ($scope.checkout.destination.distance) {
          var speed=6.0; //km/h
          $scope.checkout.expectedtime=new Date($scope.checkout.starttime.getTime()+ 3600*(0.2+ $scope.checkout.destination.distance/speed));
        } else {
          $scope.checkout.expectedtime=null;
        }
      }
      $scope.expectedOptions.minDate=$scope.checkout.starttime;
    }
  }
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
  $scope.deleteopentrip = function (boattrip,index) {
    var closetrip=DatabaseService.closeForm('trip/deleteopentrip',boattrip,'trip');
    closetrip.promise.then(function(status) {
      DatabaseService.reload(['boat']);
      if (status.status =='ok') {
        $scope.checkinmessage=status.boat+" er nu ledig, turen er slettet";
        $scope.checkin.boat=null;
        $scope.onwater.splice($scope.onwater.indexOf(boattrip),1);
      } else {
        $log.error("error "+status.message);
        $scope.checkoutmessage="Fejl: "+closetrip;
      };
    }
                          )
  }

  $scope.reusetrip = function (boat,index,km) {
    // angular bla bla send to checkout?reuse
  }

  $scope.closetrip = function (boattrip,index,km) {
    var closetrip=DatabaseService.closeForm('closetrip',boattrip,'trip');
    closetrip.promise.then(function(status) {
      DatabaseService.reload(['boat']);
      if (status.status =='ok') {
        $scope.checkinmessage= status.boat+" er nu skrevet ind";
        $scope.checkin.boat=null;
        $scope.checkout.trip_team=null;
        $scope.onwater.splice($scope.onwater.indexOf(boattrip),1);
      } else if (status.status =='error' && status.error=="notonwater") {
        $scope.checkinmessage= status.boat+" var allerede skrevet ind";
        $log.debug("not on water")
      } else {
        $log.error("error "+status.message);
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
      data.boat.trip=null;
      // DatabaseService.reload(['trip']);
      if (status.status =='ok') {
        data.boat.trip=status.tripid;
        data.boat.outtime=data.boat.outtime;

        if (status.notification){
          $scope.checkoutnotification=status.notification;
        }
        $scope.checkoutmessage= $scope.checkout.boat.name+" er nu skrevet ud "+$scope.checkout.boat.location+":";
        if ($scope.checkout.boat.placement_aisle) {
          $scope.checkoutmessage+=("Dør "+$scope.checkout.boat.placement_aisle);
        }
        if ($scope.checkout.boat.placement_level && $scope.checkout.boat.placement_level>0) {
          $scope.checkoutmessage+=(" hylde "+$scope.checkout.boat.placement_level);
        }
        if ($scope.checkout.boat.placement_row==0) {
          $scope.checkoutmessage+=(" mod porten");
        }
        if ($scope.checkout.boat.placement_side) {
          $scope.checkoutmessage+= (" "+$filter('sidetodk')($scope.checkout.boat.placement_side));
        }
        $scope.usersettime=false;
        $scope.checkout.starttime=null;
        $scope.checkout.expectedtime=null;
        for (var ir=0; ir<$scope.checkout.rowers.length; ir++) {
          $scope.checkout.rowers[ir]="";
        }
        $scope.checkout.boat=null;
        // TODO: clear
      } else if (status.status =='error' && status.error=="already on water") {
        $scope.checkouterrormessage = $scope.checkout.boat.name + " er allerede udskrevet, vælg en anden båd";
      } else if (status.status =='error' && status.error=="rower already on water") {
        $scope.checkouterrormessage = $scope.checkout.boat.name + " er allerede udskrevet, vælg en anden båd";
      } else {
        $scope.checkouterrormessage="Fejl: "+status.error;
        // TODO: give error that we could not save the trip
      };
    },function() {alert("error")}, function() {alert("notify")}
                        )
  };
  $scope.boatSync = function () {
    if (!$scope.checkout.starttime || ($scope.checkout.starttime==$scope.starttime_clean)) {
      var now = new Date();
      $scope.checkout.starttime=now;
      $scope.starttime_clean=now;
      $scope.updateExpectedTime();
    }
    var ds=DatabaseService.reload(['boat'])
    if (ds) {
      ds.then(function(what) {
        if ($scope.selectedBoatCategory) {
          $scope.selectedboats = DatabaseService.getBoatsWithCategoryName($scope.selectedBoatCategory.name);
          if ($scope.checkout.boat) {
            $log.debug("update selected boats");
            $scope.checkout.boat=DatabaseService.getBoatWithId($scope.checkout.boat.id);
            if ($scope.checkout.boat.trip) {
              $log.debug("selected boat was taken");
              $scope.checkouterrormessage="For sent: "+$scope.checkout.boat.name+" blev taget";
              $scope.checkout.boat.trip=null;
              $scope.checkout.boat=null;
            }
          }
        }
      });
    }
  }


  $scope.update_checkin_destiation = function(d) {
    $scope.checkin.update_destination_for.destination=d.name;
    $scope.checkin.update_destination_for.meter=d.distance;
    $scope.checkin.update_destination_for=null;
  }
  // Hack to handle when user clicks outside field
  // This really should be handled by better autocomplete.
  $scope.co_rower_leave = function(ix) {
    var rw=$scope.checkout.rowers[ix];
    if (typeof(rw)==="string" && rw.length<6 && rw.length>1 && rw.substring(2,6).toUpperCase()==rw.substring(2,6).toLocaleLowerCase()) {
      $scope.checkout.rowers[ix]=DatabaseService.getRower(rw.toUpperCase());
    }
    for (var ir=0; ir<ix; ir++) {
      if ($scope.checkout.rowers[ir]==$scope.checkout.rowers[ix]) {
        $scope.checkout.rowers[ix]="";
      }
    }
  }
  $scope.date_diff = function (od) {
    //      return 1000;
    return Math.round((new Date()-new Date(od))/1000/60); // minutes
  }
  $scope.test = function (data) {
    DatabaseService.test('boat');
    $scope.valid=DatabaseService.valid();
  }
  $scope.valid = function () {
    DatabaseService.valid();
  }
}
