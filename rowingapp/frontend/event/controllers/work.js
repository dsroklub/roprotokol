'use strict';
angular.module('eventApp').controller(
  'workCtrl',
  ['$scope','$routeParams','$route','DatabaseService','LoginService','$filter','ngDialog','orderByFilter','$log','$location','$anchorScroll','$timeout',
   workCtrl
  ]);

function workCtrl ($scope, $routeParams,$route,DatabaseService, LoginService, $filter, ngDialog, orderBy, $log, $location,$anchorScroll,$timeout) {
  $scope.workers=[];
  $scope.workadmin={};
  var dberr=function(err) {
    $log.debug("db init err "+err);
    if (err['error']) {
      alert('DB fejl '+err['error']);
    }
  }

  var dbready=function (ok) {
    $scope.worktasks=DatabaseService.getDB('event/worktasks');
    $scope.workers=DatabaseService.getDB('event/workers');
    $scope.maintenance_boats=DatabaseService.getDB('event/maintenance_boats');
  }
  DatabaseService.init({"fora":true,"work":true,"boat":true,"message":true,"event":true,"member":true,"user":true}).then(
    dbready,
    function(err) {$log.debug("db init err "+err)},
    function(pg) {$log.debug("db init progress  "+pg)}
  );

  $scope.getWorkersByName = function(nameorid, rowers, preselectedids) {
    var val = nameorid.trim().toLowerCase();
    if (val.length<3 && isNaN(val)) {
      return [];
    }
    if (!rowers) {
      return [];
    }
    if (isNaN(val)) {
      var re=new RegExp("\\b"+val,'i');
      var result = rowers.filter(function(element) {
        return (preselectedids === undefined || !(element.id in preselectedids)) && re.test(element['name']);
      });
      return result;
    } else {
      var result = rowers.filter(function(element) {
          return (preselectedids === undefined || !(element.id in preselectedids)) && element.id==val;
        });
      return result;
    }
  };

  $scope.update_work_req = function () {
    var sr=DatabaseService.createSubmit("set_work_req",$scope.workadmin);
    sr.promise.then(function(status) {
      if (status.status =='ok') {
        $scope.workadmin={};
      } else {
        alert(status.error);
      }
    })}

  $scope.edit_worker = function () {
  }

  $scope.select_worker = function (work) {
    var sr=DatabaseService.createSubmit("add_work",$scope.work.worker);
    sr.promise.then(function(status) {
      if (status.status =='ok') {
        $log.debug("member value updated");
        var wd=new Date();
        $scope.work.start_time=wd.toISOString();
        $scope.work.end_time=null;
        $scope.work.hours=null;
        $scope.workers.push($scope.work);
        $scope.work={};
      } else {
        alert(status.error);
      }
    }, function(err) {console.log("forum mem add work err: "+err)}
                   )
  }

  $scope.checkin_worker = function (worker) {
    worker.end_time=new Date().toISOString();
    worker.hours=(new Date(worker.end_time)-new Date(worker.start_time))/3600/1000;
    var sr=DatabaseService.createSubmit("update_work",worker);
    sr.promise.then(function(status) {
      if (status.status =='ok') {
        $log.debug("worker");
      } else {
        alert(status.error);
      }
    }, function(err) {console.log("forum mem add work err: "+err)}
                   )
  }

  $scope.generate_work = function () {
      DatabaseService.getDataNow('event/workers','generate', function (res) {
        $scope.workers=res.data;
      },dberr
                                )
  };
  $scope.delete_work = function () {
    DatabaseService.getDataNow('event/workers','delete', function (res) {
      $scope.workers=[];
    },dberr
                              )
  };
}
