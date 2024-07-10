'use strict';
angular.module('eventApp').controller(
  'rowingCtrl',
  ['$scope','$routeParams','$route','$confirm','DatabaseService','LoginService','$filter','ngDialog','orderByFilter','$log','$location','$anchorScroll','$timeout',
   rowingCtrl
  ]);
function rowingCtrl ($scope, $routeParams,$route,$confirm,DatabaseService, LoginService, $filter, ngDialog, orderBy, $log, $location,$anchorScroll,$timeout) {
    var rower_diff = function(current,correction) {
    var diffs={'from':{},'to':{}};
    angular.forEach(current, function(rid,rower,kv) {
        if (!correction[rower] || correction[rower].id!=rid.id) {
          diffs.from[rower]=rid;
        }
    },this);
    angular.forEach(correction, function(rid,rower,kv) {
      if (!current[rower] || current[rower].id!=rid.id) {
        diffs.to[rower]=rid;
      }
    },this);
    return diffs;
    }
  $scope.rodata={};
  $scope.editreservationconfiguration={'name':'-'};
  $scope.rowerkm_force_email = false;
  $scope.rowerkm_include_trips = true;
  $scope.newtriptype={"active":1,"rights":[]};
  $scope.rowerkm_separate_instruction = false;
  $scope.rowerkm_only_members = false;
  $scope.rowerkm_year = new Date().getFullYear();
  $scope.newright_year = new Date().getFullYear();
  $scope.datereservation={"start_time":"17:00","end_time":"19:00"};
  $scope.reservation_match = function() {
    return function(reservation) {
      for (var rci=0; rci<$scope.reservation_configurations.length; rci++) {
        if ($scope.reservation_configurations[rci].selected && $scope.reservation_configurations[rci].name==reservation.configuration) {
           return true;
        }
      }
      return false;
    }
  };

  var correction_diff = function(current,correction) {
    var res={'diff':{}};
    if (!correction.DeleteTrip) {
      var flds=['boat','destination','intime','outtime','distance','triptype','comment'];
      for (var ki=0; ki<flds.length;ki++) {
        var k=flds[ki];
        if (current[k]!=correction[k]) {
          res.diff[k]={'from':current[k],'to':correction[k]};
        }
      }
      if (JSON.stringify(current.rowers) != JSON.stringify(correction.rowers)) {
        res.rowerdiff=rower_diff(current.rowers,correction.rowers);
      }
    }
    return res;
  };

  $scope.dateOptions = {
    showWeeks: false,
    formatDay:"d",
    formatYear: 'yyyy',
    formatMonth: 'MMM',
    title:"dato"
  };

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

  $scope.reservations=[];
  $scope.reservation={};
  $scope.checkout={"rowers":[]};
  $scope.errortrips=[];
  $scope.trip={};
  $scope.showDestinations=["DSR","Nordhavn","Andre"];
  $scope.config={'headers':{'XROWING-CLIENT':'ROPROTOKOL'}};
  $scope.newrightdate=new Date();
  $scope.memberrighttypes=[];
  LoginService.check_user().promise.then(function(u) {
    $scope.current_user=u;
    $scope.rowerkm_force_email = false;
    $scope.rowerkm_include_trips = true;
    $scope.newtriptype={"active":1,"rights":[]};
    $scope.rowerkm_separate_instruction = false;
    $scope.rowerkm_only_members = false;
    $scope.rowerkm_year = new Date().getFullYear();
    $scope.newright_year = new Date().getFullYear();

    var wait_for_db = function (ok) {
      $scope.currentrower=null;
      $log.debug("evt db init done");
      $scope.boatcategories= [{id:101,name:"Inriggere"},{id:102,name:"Coastal"},{id:103,name:"Outriggere"},{name:"Kajakker"}];
      $scope.memberrighttypes = DatabaseService.getDB('event/memberrighttypes');
      // $log.debug("events set user " + $scope.current_user);
      LoginService.set_user($scope.current_user);
      $scope.newright={"active":1,"category":"roning"};
      $scope.do="events";
      $scope.newboat={};
      $scope.newboattype={'rights':[]};
      $scope.DB=DatabaseService.getDB;
      $scope.current_rower=DatabaseService.getCurrentRower();
      $scope.isadmin=false;
      $scope.isremote=!!$scope.current_rower;
      $scope.sculler_open=DatabaseService.getDB('event/status').sculler_open;
      $scope.destinations=DatabaseService.getDB('event/destinations')["DSR"];
      if ($scope.current_rower) {
        for (var r in $scope.current_rower.rights) {
          if ($scope.current_rower.rights[r].member_right=="admin" && $scope.current_rower.rights[r].arg=="roprotokol") {
            $scope.isadmin=true;
            break;
          }
        }
      }
      $scope.ready=true;
      $scope.triptypes=DatabaseService.getDB('event/triptypes');
      $scope.zones=DatabaseService.getDB('event/zones');
      $scope.reservations = DatabaseService.getDB('get_reservations');
      $scope.reservation_configurations = DatabaseService.getDB('event/reservations/reservation_configurations');
      $scope.clientname = DatabaseService.client_name();
      $scope.boats={"allboats":DatabaseService.getDB('boatsA')};
      $scope.iboats=DatabaseService.getDB('boats');
      $scope.locations = DatabaseService.getDB('event/locations');
      $scope.rowevents = DatabaseService.getDB('event/get_row_events');
      $scope.memberrighttypes = DatabaseService.getDB('event/memberrighttypes');
      $scope.boatkayakcategories = DatabaseService.getDB('event/boatkayakcategory');
      $scope.rights_subtypes = DatabaseService.getDB('event/rights_subtype');
      $scope.errortrips = DatabaseService.getDB('event/errortrips');
      $scope.levels=DatabaseService.getDB('boatlevels');
      $scope.brands=DatabaseService.getDB('event/boat_brand');
      $scope.usages=DatabaseService.getDB('event/boat_usages');
      $scope.placementlevels=[0,1,2,4,3];
      $scope.ziperrors=[];
      $scope.getTriptypeWithID=DatabaseService.getTriptypeWithID;
      for (var rci=0; rci<$scope.reservation_configurations.length; rci++) {
	  if ($scope.reservation_configurations[rci].name=='altid') {
	      $scope.datereservation.configuration=$scope.reservation_configurations[rci];
	  }
      }
    var i=0;
    while (i < $scope.errortrips.length -1) {
      while ($scope.errortrips[i].id && i < $scope.errortrips.length-1) {
        i++;
      }
      var j=i+1;
      while (j<$scope.errortrips.length && $scope.errortrips[j].Trip==$scope.errortrips[i].Trip) {
        $scope.ziperrors.push({
          'trip':$scope.errortrips[i].Trip,
          'current':$scope.errortrips[i],
          'correction':$scope.errortrips[j],
          'diffs': correction_diff($scope.errortrips[i],$scope.errortrips[j])
        });
        j++;
      }
      i++;
    }
      $scope.boatcategories = DatabaseService.getDB('event/boattypes');

    $scope.errorhandler = function(error) {
      $log.error(error);
      if (error.status==400 || error.status=="notauthorized") {
        $route.reload();
        alert("du skal logge ind");
      } else {
        alert("DB fejl " + error.data.error);
      }
    }

    $scope.getRowerByName = function (val) {
      return DatabaseService.getRowersByNameOrId(val, undefined);
    };

    $scope.boattype_update = function(bt) {
      DatabaseService.updateDB('event/boats/boattype_update',bt,$scope.config,$scope.errorhandler)
        .then(function(){
          bt.changed = false
        });
    }

    $scope.toggle_rc = function(rc) {
     if (rc.name=='altid') return;
      rc.selected=!rc.selected;
      var exeres=DatabaseService.updateDB('event/reservations/set_reservation_configuration',rc,$scope.config,$scope.errorhandler);
    }

    $scope.update_res = function(rv) {
      var exeres=DatabaseService.updateDB('event/reservations/update_reservation',rv,$scope.config,$scope.errorhandler);
    }

    $scope.create_boattype = function(bt) {
      var exeres=DatabaseService.updateDB('event/boats/create_boattype',bt,$scope.config,$scope.errorhandler);
      $scope.DB('boattypes').push(bt);
      $scope.newboattype={'rights':[]};
    }

    $scope.create_destination = function(dest) {
      $log.info("new destination");
      var exeres=DatabaseService.updateDB('event/trips/create_destination',dest,$scope.config,$scope.errorhandler);
      if (!$scope.DB('destinations')[dest.location]) {
        $scope.DB('destinations')[dest.location]=[];
      }
      $scope.DB('destinations')[dest.location].push(dest);
      $scope.newdestination={};
    }

    $scope.create_boat_brand = function(bb) {
      var exeres=DatabaseService.updateDB('event/boats/create_boat_brand',bb,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          $scope.DB('boat_brand').push(bb);
        }
      });
    }

    $scope.create_triptype = function(tt) {
      var exeres=DatabaseService.updateDB('event/create_triptype',tt,$scope.config,$scope.errorhandler).then(
        function(newtriptype) {
          if (newtriptype.status=="ok") {
            tt.id=newtriptype.triptypeid;
            $scope.triptypes.unshift(tt);
            $scope.newtriptype={"active":1,"rights":[]};
          }
        }
      )
    }

    $scope.update_level = function(boat) {
      var exeres=DatabaseService.updateDB('event/boats/boat_update_level',boat,$scope.config,$scope.errorhandler);
    }
    $scope.update_brand = function(boat) {
      var exeres=DatabaseService.updateDB('event/boats/boat_update_brand',boat,$scope.config,$scope.errorhandler);
    }
    $scope.update_usage = function(boat) {
      var exeres=DatabaseService.updateDB('event/boats/boat_update_usage',boat,$scope.config,$scope.errorhandler);
    }

    $scope.update_usage_name = function(boat) {
      var exeres=DatabaseService.updateDB('event/boats/usage_update_name',boat,$scope.config,$scope.errorhandler);
    }
    $scope.update_usage_description = function(usage) {
      var exeres=DatabaseService.updateDB('event/boats/usage_update_description',usage,$scope.config,$scope.errorhandler);
    }
    $scope.update_usage_name = function(usage) {
      var exeres=DatabaseService.updateDB('event/boats/usage_update_name',usage,$scope.config,$scope.errorhandler);
    }
    $scope.set_sculler_open = function(sculler_open) {
      var exeres=DatabaseService.updateDB('event/trips/set_sculler_open',sculler_open,$scope.config,$scope.errorhandler);
    }
    $scope.set_reservation_configuration = function(resconf) {
      var exeres=DatabaseService.updateDB('event/reservations/set_reservation_configuration',resconf,$scope.config,$scope.errorhandler);
    }
    $scope.create_usage = function(usage) {
      $log.info('create new usage '+usage);
      var exeres=DatabaseService.updateDB('event/boats/usage_create',usage,$scope.config,$scope.errorhandler).then(
        function(newusage) {
          if (newusage.status=="ok") {
            $scope.usages.push(newusage.newusage);
            usage.name="";
            usage.description="";
          }
        }
      );
    }

    $scope.set_destination_name = function(destination) {
      var exeres=DatabaseService.updateDB('event/trips/set_destination_name',destination,$scope.config,$scope.errorhandler);
      if (confirm("omdøb " + destination.orig_name + " til " + destination.orig_name)) {
        exeres.then(
          function (status) {
            if (status.status=="ok") {
              destination.orig_name=destination.name;
            }
          });
      } else {
        destination.name=destination.orig_name;
      }
    }

    $scope.set_duration = function(destination) {
      var exeres=DatabaseService.updateDB('event/trips/set_duration',destination,$scope.config,$scope.errorhandler);
    }
    $scope.set_zone = function(zone) {
      var exeres=DatabaseService.updateDB('event/trips/set_zone',zone,$scope.config,$scope.errorhandler);
    }
    $scope.set_distance = function(destination) {
      var exeres=DatabaseService.updateDB('event/trips/set_distance',destination,$scope.config,$scope.errorhandler);
    }

    $scope.set_cat_for_boat = function(boat) {
      var exeres=DatabaseService.updateDB('event/boats/set_cat_for_boat',boat,$scope.config,$scope.errorhandler);
    }
    $scope.set_name_for_boat = function(boat) {
      $confirm({text: 'Vil du omdøbe båden til'+boat.name+'?'})
        .then(function() {
          var exeres=DatabaseService.updateDB('event/boats/set_name_for_boat',boat,$scope.config,$scope.errorhandler);
        });
    }
    $scope.set_loc_for_boat = function(boat) {
      var exeres=DatabaseService.updateDB('event/boats/set_loc_for_boat',boat,$scope.config,$scope.errorhandler);
    }
    $scope.set_aisle_for_boat = function(boat) {
      var exeres=DatabaseService.updateDB('event/boats/set_aisle_for_boat',boat,$scope.config,$scope.errorhandler);
    }
    $scope.set_row_for_boat = function(boat) {
      var exeres=DatabaseService.updateDB('event/boats/set_row_for_boat',boat,$scope.config,$scope.errorhandler);
    }
    $scope.set_level_for_boat = function(boat) {
      var exeres=DatabaseService.updateDB('event/boats/set_level_for_boat',boat,$scope.config,$scope.errorhandler);
    }
    $scope.set_side_for_boat = function(boat) {
      var exeres=DatabaseService.updateDB('event/boats/set_side_for_boat',boat,$scope.config,$scope.errorhandler);
    }
    $scope.set_boat_note = function(boat) {
      var exeres=DatabaseService.updateDB('event/boats/set_boat_note',boat,$scope.config,$scope.errorhandler);
    }

    $scope.retire_boat = function(boat) {
      var exeres=DatabaseService.updateDB('event/boats/retire_boat',boat,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          boat.location=null;
          boat.placement_aisle=null;
          boat.placement_level=null;
          boat.placement_row=null;
          boat.placement_side=null;
        }
      });
    }
    $scope.unretire_boat = function(boat) {
      var exeres=DatabaseService.updateDB('event/boats/unretire_boat',boat,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          boat.location="DSR";
        }
      });
    }

    $scope.create_boat = function(boat) {
      var exeres=DatabaseService.updateDB('event/boats/create_boat',boat,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          boat.id=status.boat_id;
          $scope.boats.allboats.push(boat);
          $scope.newboat={};
        }
      });
    }

    $scope.set_client_name =function(name) {
      if (localStorage) {
        localStorage.setItem("roprotokol.client.name",name);
      }
    }

    $scope.add_rower_right = function(right,rower,nrd) {
      var data={'right':right,'rower':rower,'newrightdate':nrd.toISOString().split('T')[0]}
      var exeres=DatabaseService.updateDB('event/rights/add_rower_right',data,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          $scope.currentrower.rights.unshift(right);
        }
      });
    }

    $scope.remove_rower_right = function(right,rower,ix) {
      var data={'right':right,'rower':rower}
      var exeres=DatabaseService.updateDB('event/rights/remove_rower_right',data,$scope.config,$scope.errorhandler).then(function(status) {
        if (status && status.status=="ok") {
          $scope.currentrower.rights.splice(ix,1);
        }
      },$scope.errorhandler);
    }

    $scope.remove_boattype_requirement = function(rt,ix) {
      var data={"boat_type":$scope.currentboattype,'right':rt};
      var exeres=DatabaseService.updateDB('event/rights/remove_boattype_right',data,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          $scope.requiredboatrights.splice(ix,1);
        }
      });
    }

    $scope.add_boattype_requirement = function(data,existing_rights) {
      data.boat_type=$scope.currentboattype;
      var exeres=DatabaseService.updateDB('event/boats/add_boattype_req',data,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          existing_rights.push({"requirement":data.subject, "required_right":data.right});
        }
      });
    }

    $scope.add_triptype_requirement = function(data,existing_rights) {
      data.triptype=$scope.currenttriptype;
      var rr=data.right;
      var rs=data.subject;
      var exeres=DatabaseService.updateDB('event/rights/add_triptype_req',data,$scope.config,$scope.errorhandler).then(
        function(status) {
          if (status.status=="ok") {
            existing_rights.push({"requirement":rs, "required_right":rr});
          }
        },
        $scope.errorhandler
      );
    }
    $scope.remove_triptype_requirement = function(rt,ix) {
      var data={triptype:$scope.currenttriptype,'right':rt};
      var exeres=DatabaseService.updateDB('event/rights/remove_triptype_req',data,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          delete $scope.requiredtriprights.splice(ix,1);
        }
      });
    }

    $scope.update_triptype_requirement = function(tt,tr) {
      var exeres=DatabaseService.updateDB('event/rights/update_triptype_req',{"triptype":tt,"req":tr},$scope.config,$scope.errorhandler);
    }
    $scope.update_boattype_requirement = function(bt,br) {
      var exeres=DatabaseService.updateDB('event/rights/update_boattype_requirement',{"boattype":bt,"req":br},$scope.config,$scope.errorhandler);
    }
    $scope.approve_correction = function(data,ix) {
      var exeres=DatabaseService.updateDB('event/trips/approve_correction',data,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          $scope.ziperrors.splice(ix,1);
        }
      });
    }

    $scope.reject_correction = function(data,ix) {
      var exeres=DatabaseService.updateDB('event/trips/reject_correction',data,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          $scope.ziperrors.splice(ix,1);
        }
      });
    }

    $scope.boatcat2dk=DatabaseService.boatcat2dk;
    $scope.rightsubjects=['cox','all','any','none'];

    $scope.rowerconvert = function (fromrower,torower) {
      if (fromrower && torower) {
        if (fromrower.id != torower.id) {
          DatabaseService.updateDB('event/convert_rower',{"from":fromrower,"to":torower},$scope.config,$scope.errorhandler)
            .then(function(status){
              if (status.status=="ok") {
                $scope.converttorower=null;
                $scope.currentrower=null;
                DatabaseService.removeRower(fromrower);
                alert("Konverteringen lykkedes");
              }
            });
        } else {
          alert("roerne skal være forskellige");
        }
      } else {
        alert("begge roere skal være valgt");
      }
    }

    $scope.doboatrights = function (rr,bt) {
      if (rr&rr.length==0) {
        $scope.requiredboatrights={};
      } else {
        $scope.requiredboatrights=rr;
      }
      $scope.currentboattype=bt;
    }

    $scope.noright= function() {
      return function(rtt) {
        if (!rtt.active) {
          return false;
        }
        for (var ri=0;ri<$scope.currentrower.rights.length;ri++) {
          var rr=$scope.currentrower.rights[ri];
          if (rr.member_right==rtt.member_right && rr.arg==rtt.arg) {
            return false;
          }
        }
        return true;
      }
    }

    $scope.noreq= function(allreqs) {
      return function(rtt) {
        if (!allreqs) return false;
        for (var i=0; i<allreqs.length; i++) {
          if (allreqs[i].required_right==rtt.member_right) {
            return false;
          }
        }
        return true;
      }
    }

    $scope.make_reservation = function (reservation){
      var r=angular.copy(reservation);
      r.start_time=$filter('date')(reservation.start_time,"HH:mm");
      r.end_time=$filter('date')(reservation.end_time,"HH:mm");
      if (r.end_date) {
        r.end_date=DatabaseService.toIsoDate(reservation.end_date);
      }
      if (r.start_date) {
        r.start_date=DatabaseService.toIsoDate(reservation.start_date);
      }
      $scope.editreservationconfiguration=reservation.configuration;
      var exeres=DatabaseService.updateDB('event/reservations/make_reservation',r,$scope.config,$scope.errorhandler).then(
        function(newreservation) {
          if (newreservation.status=="ok") {
            $log.info("reservation made");
            r.configuration=r.configuration.name;
            r.id=newreservation.reservationid;
	    var rc=angular.copy(r);
            if (rc.start_time && rc.start_time.timestring) {
              rc.start_time=rc.start_time.timestring;
            }
            if (rc.end_time && rc.end_time.timestring) {
              rc.end_time=rc.end_time.timestring;
            }
            $scope.reservations.unshift(rc);
          }
        }
      )            }

    $scope.make_righttype = function (right){
      var r=angular.copy(right);
      var exeres=DatabaseService.updateDB('event/rights/create_right',r,$scope.config,$scope.errorhandler).then(
        function(newright) {
          if (newright.status=="ok") {
            $log.info("right created");
            $scope.memberrighttypes.unshift(r);
            $scope.newright={"active":1,"category":"roning"};
          }
        }
      )            }

    $scope.cancel_reservation = function (rv){
      var r=angular.copy(rv);
      var exeres=DatabaseService.updateDB('event/reservations/cancel_reservation',r,$scope.config,$scope.errorhandler).then(
        function(res) {
          if (res.status=="ok") {
            $log.info("reservation canceled");
            $scope.reservations.splice($scope.reservations.indexOf(rv),1);
          }
        }
      )            }

    $scope.dotriprights = function (rr,tt) {
      $scope.requiredtriprights=rr;
      $scope.currenttriptype=tt;
    }

    $scope.matchBoatType = function(cat) {
      return function(matchboat) {
        return (matchboat.id  && cat && matchboat.category==cat.name);
      }
    };
    $scope.dotripactive = function (tt) {
      var exeres=DatabaseService.updateDB('event/trips/activate_triptype',tt,$scope.config,$scope.errorhandler);
    }
    };

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
      
    $scope.createtrip = function (data) {
       $scope.checkout.boat=null;
      if ($scope.rightsmessage && $scope.rightsmessage.length>0) {
        data.event=$scope.rightsmessage;
      }
    var newtrip=DatabaseService.createSubmit('registertrip',data);
    newtrip.promise.then(function(status) {
      data.boat.trip=null;
      if (status.status =='ok') {
        $scope.checkouterrormessage=null;
        $scope.washmessage="";
        data.boat.trip=status.tripid;
        data.boat.outtime=data.boat.outtime;
        $scope.checkoutnotification=null;
        if (status.notification){
          $scope.checkoutnotification=status.notification;
        }
        $scope.checkoutmessage= $scope.checkout.boat.name+" er nu skrevet ud "+$scope.checkout.boat.location+": ";
        $scope.usersettime=false;
        $scope.checkout.starttime=null;
        $scope.checkout.expectedtime=null;
        for (var ir=0; ir<$scope.checkout.rowers.length; ir++) {
          $scope.checkout.rowers[ir]="";
        }
        $scope.checkout.boat=null;
      } else {
        $scope.checkouterrormessage="Fejl: "+status.error;
      };
    },function() {alert("error")}, function() {alert("notify")}
                        )
  };

    $scope.do_boat_category = function(cat) {
      $scope.checkoutmessage=null;
      $scope.checkoutnotification=null;
      for (var i = $scope.checkout.rowers.length; i < cat.seatcount; i++) {
        $scope.checkout.rowers.push("");
      }
      $scope.checkout.rowers=$scope.checkout.rowers.splice(0,cat.seatcount);
    }

      $scope.isName = function(n) {
    if (!n) {
      return false;
    }
    if (n.length>3 && isNaN(n)) {
      return true;
    }
    return false;
  };

    $scope.createRower = function (rowers,index,temptype,club) {
      var inputname=rowers[index];
      if (inputname.toLowerCase().indexOf("gæst")>=0) {
        rowers[index]="";
        alert("Roeren hedder ikke gæst. Brug " + (temptype=="guest"?"gæstens":"kaninens")  +  " rigtige navn");
        return;
      }
      if (inputname.toLowerCase().indexOf("kanin")>=0) {
        rowers[index]="";
        alert("Roeren hedder ikke kanin. Brug " + (temptype=="guest"?"gæstens":"kaninens")  +  " rigtige navn");
        return;
      }
      if (/\d/.test(inputname)) {
        rowers[index]="";
        alert("navnet indeholder tal. Brug " + (temptype=="guest"?"gæstens":"kaninens")  +  " rigtige navn");
        return;
      }
      var tmpnames=inputname.trim().split(" ");
      var last=tmpnames.splice(-1,2)[0];
      var first=tmpnames.join(" ");
      var rowerreq={
        "firstName":first,
        "lastName":last,
        "type":temptype,
        "guest_club":club
      }
      var rower = DatabaseService.updateDB_async('event/createrower',rowerreq).then(
        function(rower) {
          if (rower.error) {
            $scope.checkoutmessage=rower.error;
            var errSnd=document.querySelector("#noboat");
            errSnd.play();
          } else {
            $scope.checkout.rowers[index] = rower;
          }
        }
      );
    };

      DatabaseService.init({"admin":true,"trip":true,"status":true,"reservation":true,"fora":false,"file":false,"boat":true,"message":false,"event":false,"member":true,"user":true,"destination":true}).then(
      wait_for_db,
      function(err) {$log.debug("db init err "+err)},
      function(pg) {$log.debug("db init progress  "+pg)}
    );
    // $scope.current_user.is_winter_admin=null;// FIXME REMOVE
  });
}

