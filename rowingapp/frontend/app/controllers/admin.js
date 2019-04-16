'use strict';

angular.module('rowApp').controller(
    'AdminCtrl',
  ['$scope', 'DatabaseService', 'NgTableParams', '$filter', '$route', '$confirm','$log',
   AdminCtrl
  ]);

function AdminCtrl ($scope, DatabaseService, NgTableParams, $filter,$route,$confirm,$log) {
  var rower_diff = function(current,correction) {
    var diffs={'from':{},'to':{}};
    angular.forEach(current, function(rid,rower,kv) {
      if (correction[rower].id!=rid.id) {
        diffs.from[rower]=rid;
      }
    },this);
    angular.forEach(correction, function(rid,rower,kv) {
      if (current[rower].id!=rid.id) {
        diffs.to[rower]=rid;
      }
    },this);
    return diffs;
  }

  $scope.rowerkm_force_email = false;
  $scope.rowerkm_include_trips = true;
  $scope.rowerkm_separate_instruction = false;
  $scope.rowerkm_only_members = false;
  $scope.rowerkm_year = new Date().getFullYear();


  $scope.reservation_current = function() {
    return function(reservation) {
      return (reservation.dayofweek<1 || reservation.configuration==$scope.config.reservation_configuration);
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
  $scope.errortrips=[];
  $scope.trip={};
  $scope.showDestinations=["DSR","Nordhavn","Andre"];
  $scope.config={'headers':{'XROWING-CLIENT':'ROPROTOKOL'}};

  DatabaseService.init({"boat":true,"status":true,"member":true, "trip":true,"reservation":true}).then(function () {
    $scope.currentrower=null;
    $scope.do="events";
    $scope.newboat={};
    $scope.newboattype={'rights':[]};
    $scope.DB=DatabaseService.getDB;
    $scope.current_rower=DatabaseService.getCurrentRower();
    $scope.isadmin=false;
    $scope.sculler_open=DatabaseService.getDB('status').sculler_open;
    $scope.config.reservation_configuration=DatabaseService.getDB('status').reservation_configuration;
    if ($scope.current_rower) {
      for (var r in $scope.current_rower.rights) {
        if ($scope.current_rower.rights[r].member_right=="admin" && $scope.current_rower.rights[r].arg=="roprotokol") {
          $scope.isadmin=true;
          break;
        }
      }
    }
    $scope.triptypes=DatabaseService.getDB('triptypes');
    $scope.reservations = DatabaseService.getDB('get_reservations');
    $scope.clientname = DatabaseService.client_name();
    $scope.boats={"allboats":DatabaseService.getDB('boatsA')};
    $scope.iboats=DatabaseService.getDB('boats');
    $scope.locations = DatabaseService.getDB('locations');
    $scope.events = DatabaseService.getDB('get_events');
    $scope.memberrighttypes = DatabaseService.getDB('memberrighttypes');
    $scope.boatkayakcategories = DatabaseService.getDB('boatkayakcategory');
    $scope.rights_subtypes = DatabaseService.getDB('rights_subtype');
    $scope.errortrips = DatabaseService.getDB('errortrips');
    $scope.levels=DatabaseService.getDB('boatlevels');
    $scope.brands=DatabaseService.getDB('boat_brand');
    $scope.usages=DatabaseService.getDB('boat_usages');
    $scope.placementlevels=[0,1,2];
    $scope.ziperrors=[];
    $scope.getTriptypeWithID=DatabaseService.getTriptypeWithID;
    // var mainplan=[[],[],[],[],[]];
    // var num_aisles=5;
    // var num_rows=3;
    // for (var a=0;a<num_aisles;a++) {
    //   mainplan[a]=[];
    //   for (var r=0;r<num_rows;r++) {

    //   }
    // }

    // for (var b=0;b<$scope.allboats.length;b++ ) {

    // }

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
    $scope.boatcategories = DatabaseService.getBoatTypes();

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
      DatabaseService.updateDB('boattype_update',bt,$scope.config,$scope.errorhandler)
        .then(function(){
          bt.changed = false
        });
    }

    $scope.create_boattype = function(bt) {
      var exeres=DatabaseService.updateDB('create_boattype',bt,$scope.config,$scope.errorhandler);
      $scope.DB('boattypes').push(bt);
      $scope.newboattype={'rights':[]};
    }

    $scope.create_destination = function(dest) {
      $log.info("new destination");
      var exeres=DatabaseService.updateDB('create_destination',dest,$scope.config,$scope.errorhandler);
      if (!$scope.DB('destinations')[dest.location]) {
        $scope.DB('destinations')[dest.location]=[];
      }
      $scope.DB('destinations')[dest.location].push(dest);
      $scope.newdestination={};
    }

    $scope.create_boat_brand = function(bb) {
      var exeres=DatabaseService.updateDB('create_boat_brand',bb,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          $scope.DB('boat_brand').push(bb);
        }
      });
    }

    $scope.create_triptype = function(tt) {
      $log.info("new triptype");
      var exeres=DatabaseService.updateDB('create_triptype',tt,$scope.config,$scope.errorhandler);
      $scope.triptypes.push(tt);
    }

    $scope.update_level = function(boat) {
      var exeres=DatabaseService.updateDB('boat_update_level',boat,$scope.config,$scope.errorhandler);
    }
    $scope.update_brand = function(boat) {
      var exeres=DatabaseService.updateDB('boat_update_brand',boat,$scope.config,$scope.errorhandler);
    }
    $scope.update_usage = function(boat) {
      var exeres=DatabaseService.updateDB('boat_update_usage',boat,$scope.config,$scope.errorhandler);
    }

    $scope.update_usage_name = function(boat) {
      var exeres=DatabaseService.updateDB('usage_update_name',boat,$scope.config,$scope.errorhandler);
    }
    $scope.update_usage_description = function(usage) {
      var exeres=DatabaseService.updateDB('usage_update_description',usage,$scope.config,$scope.errorhandler);
    }
    $scope.update_usage_name = function(usage) {
      var exeres=DatabaseService.updateDB('usage_update_name',usage,$scope.config,$scope.errorhandler);
    }
    $scope.set_sculler_open = function(sculler_open) {
      var exeres=DatabaseService.updateDB('set_sculler_open',sculler_open,$scope.config,$scope.errorhandler);
    }
    $scope.set_reservation_configuration = function(resconf) {
      var exeres=DatabaseService.updateDB('set_reservation_configuration',resconf,$scope.config,$scope.errorhandler);
    }
    $scope.create_usage = function(usage) {
      $log.info('create new usage '+usage);
      var exeres=DatabaseService.updateDB('usage_create',usage,$scope.config,$scope.errorhandler).then(
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
      var exeres=DatabaseService.updateDB('set_destination_name',destination,$scope.config,$scope.errorhandler);
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
      var exeres=DatabaseService.updateDB('set_duration',destination,$scope.config,$scope.errorhandler);
    }
    $scope.set_distance = function(destination) {
      var exeres=DatabaseService.updateDB('set_distance',destination,$scope.config,$scope.errorhandler);
    }

    $scope.set_cat_for_boat = function(boat) {
      var exeres=DatabaseService.updateDB('set_cat_for_boat',boat,$scope.config,$scope.errorhandler);
    }
    $scope.set_name_for_boat = function(boat) {
      $confirm({text: 'Vil du omdøbe båden til'+boat.name+'?'})
        .then(function() {
          var exeres=DatabaseService.updateDB('set_name_for_boat',boat,$scope.config,$scope.errorhandler);
        });
    }
    $scope.set_loc_for_boat = function(boat) {
      var exeres=DatabaseService.updateDB('set_loc_for_boat',boat,$scope.config,$scope.errorhandler);
    }
    $scope.set_aisle_for_boat = function(boat) {
      var exeres=DatabaseService.updateDB('set_aisle_for_boat',boat,$scope.config,$scope.errorhandler);
    }
    $scope.set_row_for_boat = function(boat) {
      var exeres=DatabaseService.updateDB('set_row_for_boat',boat,$scope.config,$scope.errorhandler);
    }
    $scope.set_level_for_boat = function(boat) {
      var exeres=DatabaseService.updateDB('set_level_for_boat',boat,$scope.config,$scope.errorhandler);
    }
    $scope.set_side_for_boat = function(boat) {
      var exeres=DatabaseService.updateDB('set_side_for_boat',boat,$scope.config,$scope.errorhandler);
    }

    $scope.retire_boat = function(boat) {
      var exeres=DatabaseService.updateDB('retire_boat',boat,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          $scope.boats.allboats.splice($scope.boats.allboats.indexOf(boat),1);
        }
      });
    }

    $scope.create_boat = function(boat) {
      var exeres=DatabaseService.updateDB('create_boat',boat,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          boat.id=status.boat_id;
          $scope.boats.allboats.push(boat);
          $scope.newboat={};
        }
      });
    }

    $scope.set_client_name =function(name) {
      if (localStorage) {
        $scope.clientname=localStorage.setItem("roprotokol.client.name",$scope.clientname);
      }
    }

    $scope.add_rower_right = function(right,rower) {
      var data={'right':right,'rower':rower}
      var exeres=DatabaseService.updateDB('add_rower_right',data,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          $scope.currentrower.rights.push(right);
        }
      });
    }

    $scope.remove_rower_right = function(right,rower,ix) {
      var data={'right':right,'rower':rower}
      var exeres=DatabaseService.updateDB('remove_rower_right',data,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          $scope.currentrower.rights.splice(ix,1);
        }
      });
    }

    $scope.remove_boattype_requirement = function(rt,ix) {
      var data={"boat_type":$scope.currentboattype,'right':rt};
      var exeres=DatabaseService.updateDB('remove_boattype_right',data,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          $scope.requiredboatrights.splice(ix,1);
        }
      });
    }

    $scope.add_boattype_requirement = function(data,existing_rights) {
      data.boat_type=$scope.currentboattype;
      var exeres=DatabaseService.updateDB('add_boattype_req',data,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          existing_rights.push({"requirement":data.subject, "required_right":data.right});
        }
      });
    }

    $scope.remove_triptype_requirement = function(rt,ix) {
      var data={triptype:$scope.currenttriptype,'right':rt};
      var exeres=DatabaseService.updateDB('remove_triptype_req',data,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          delete $scope.requiredtriprights.splice(ix,1);
        }
      });
    }

    $scope.add_triptype_requirement = function(data) {
      data.triptype=$scope.currenttriptype;
      $scope.requiredtriprights.push({"required_right":data.right, "requirement":data.subject});
      var exeres=DatabaseService.updateDB('add_triptype_req',data,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          $scope.trip.newright.right=null;
        }
      });
    }

    $scope.approve_correction = function(data,ix) {
      var exeres=DatabaseService.updateDB('approve_correction',data,$scope.config,$scope.errorhandler).then(function(status) {
        if (status.status=="ok") {
          $scope.ziperrors.splice(ix,1);
        }
      });
    }

    $scope.reject_correction = function(data,ix) {
      var exeres=DatabaseService.updateDB('reject_correction',data,$scope.config,$scope.errorhandler).then(function(status) {
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
          DatabaseService.updateDB('convert_rower',{"from":fromrower,"to":torower},$scope.config,$scope.errorhandler)
            .then(function(status){
              if (status.status=="ok") {
                $scope.converttorower=null;
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
        return (rtt&&$scope.currentrower && typeof($scope.currentrower.rights[rtt.member_right])!=="string");
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

      var exeres=DatabaseService.updateDB('make_reservation',r,$scope.config,$scope.errorhandler).then(
        function(newreservation) {
          if (newreservation.status=="ok") {
            $log.info("reservation made");
            console.log("Reservation made");
            r.configuration=$scope.config.reservation_configuration;
            $scope.reservations.push(r);
            $scope.reservation.boat_id=null;
          }
        }
      )            }


    $scope.cancel_reservation = function (rv){
      var r=angular.copy(rv);
      var exeres=DatabaseService.updateDB('cancel_reservation',r,$scope.config,$scope.errorhandler).then(
        function(res) {
          if (res.status=="ok") {
            $log.info("reservation canceled");
            $scope.reservations.splice($scope.reservations.indexOf(rv),1);
          }
        }
      )            }

    $scope.dotriprights = function (rr,tt) {
      if (rr && rr.length==0) { // Hack, must be due to PHP json marshalling
        $scope.requiredtriprights={};
      } else {
        $scope.requiredtriprights=rr;
      }
      $scope.currenttriptype=tt;
    }

    $scope.matchBoatType = function(cat) {
      return function(matchboat) {
        return (matchboat.id  && cat && matchboat.category==cat.name);
      }
    };
    $scope.dotripactive = function (tt) {
      var exeres=DatabaseService.updateDB('activate_triptype',tt,$scope.config,$scope.errorhandler);
    }
  }                                                                                        )
}
