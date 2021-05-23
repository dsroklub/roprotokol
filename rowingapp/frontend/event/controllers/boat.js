'use strict';
// Niels Elgaard Larsen, v2

angular.module('eventApp').controller(
  'boatCtrl',
  ['$scope','$routeParams','$route','DatabaseService','LoginService','$filter','ngDialog','orderByFilter','$log','$location','$anchorScroll','$timeout','UploadBase',
   boatCtrl
  ]);

function boatCtrl ($scope, $routeParams,$route,DatabaseService, LoginService, $filter, ngDialog, orderBy, $log, $location,$anchorScroll,$timeout,UploadBase) {
  $scope.errorhandler = function(error) {
    $log.error(error);
    if (error.status==400 || error.status=="notauthorized") {
      $route.reload();
      alert("du skal logge ind");
    } else {
      alert("DB fejl " + error.data.error);
    }
  }
  $anchorScroll.yOffset = 50;
  $scope.boatObj=null;
  $scope.searchdamage={boattype:"all",degree:-1};

  $scope.todpattern="[0-2]\\d:[0-5]\\d";
  $scope.boats=[];
  $scope.boatdamages=[];
  $scope.current_boat_type={'id':null,'name':null};
  $scope.newdamage={};
  $scope.dateOptions = {
    showWeeks: false,
    minDate: $scope.min_time
  };
  $scope.enddateOptions = {
    showWeeks: false,
    minDate: $scope.min_time
  };
  $scope.init = function() {
  }
  $scope.dbready=false;
  $scope.init();
  $scope.dbgrace=true;
  $timeout(function() { $scope.dbgrace = false;}, 2000);

  var wait_for_db = function (ok) {
    $log.debug("evt boat db init done");
    $scope.boatcategories=[];
    $log.debug("EVET BT");
    $scope.boats=DatabaseService.getDB('event/boats');
    $log.debug("get dam");
    $scope.boatdamages=DatabaseService.getDB('event/boatdamages');
    $scope.damage_types=DatabaseService.getDB('event/damage_types');
    $scope.boats=DatabaseService.getDB('event/boats');
    $scope.boatcategories = DatabaseService.getDB('event/boattypes');
    $scope.member_setting=DatabaseService.getDB('event/member_setting');
    LoginService.check_user().promise.then(function(u) {
      $scope.current_user=u;
    });
    $log.debug("EVT BOAT DB READY");
    $scope.dbready=true;
    LoginService.set_user($scope.current_user);
  };

  $scope.update_damage = function(damage) {
    DatabaseService.updateDB('damage_update',damage,$scope.config,$scope.errorhandler);
  }

    $scope.matchBoat = function(boat) {
    return function(matchboat) {
      return (matchboat.id && (boat==null || matchboat.boat_id==boat.id));
    }
  };

  $scope.matchType = function(boat,boat_type) {
    return function(matchboat) {
      return (matchboat.boat_type && (!boat_type || matchboat.boat_type==boat_type.name) && (!boat || boat.name==matchboat.boat));
    }
  };

  $scope.matchBoatAndType = function(boat,boat_type) {
    return function(matchboat) {
      return (matchboat.id && (boat==null || matchboat.boat_id==boat.id) && (!boat_type || matchboat.boat_type==boat_type.name));
    }
  };

  $scope.matchDegree = function(d) {
    return function(md) {
      return (md.degree>=d);
    }
  };

  $scope.reportFixDamage = function (bd,damagelist) {
    // reporter is an argument so that it works when calling from checkout is implementerd
    if (bd) {
      var data={
        "damage":bd,
      }
      DatabaseService.createSubmit('fixdamage',data).promise.then(function(status) {
        if (status.status =='ok') {
          damagelist.splice(damagelist.indexOf(bd),1);
          $scope.damagesnewstatus="klarmeldt";
        } else {
          $scope.damagesnewstatus="Database fejl under klarmelding";
        }
      },$scope.errorhandler);
    }
  };

  $scope.getMatchingBoatsWithType = function (vv,boat_type) {
    var result = $scope.boats.filter(function(boat) {
          return ( boat['name'].toLowerCase().indexOf(vv.toLowerCase()) == 0  && (!boat_type || boat_type.name==boat.category));
        });
    return result;
  };

  $scope.reportDamageForBoat = function (damage) {
    if (damage.degree && damage.boat && damage.description) {
      $scope.damagesnewstatus="OK";
      var exeres=DatabaseService.updateDB_async('event/newdamage',damage,$scope.config).then(
        function(data) {
          if (data.status=="ok") {
            data.damage.damage_name=$scope.damage_types[data.damage.degree-1].name;
            $scope.boatdamages.splice(0,0,data.damage);
            $scope.newdamage={};
          }
        }
      )
    } else {
      $scope.damagesnewstatus="alle felterne skal udfyldes";
    }
  };

  DatabaseService.init({"fora":false,"file":false,"boat":true,"message":false,"event":false,"member":true,"user":true}).then(
    wait_for_db,
    function(err) {$log.debug("db init err "+err)},
    function(pg) {$log.debug("db init progress  "+pg)}
  );


}
