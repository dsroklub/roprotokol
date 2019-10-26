'use strict';
angular.module('eventApp').controller(
  'workCtrl',
  ['$scope','$routeParams','$route','DatabaseService','LoginService','$filter','ngDialog','orderByFilter','$log','$location','$anchorScroll','$timeout',
   workCtrl
  ]);

function parseDateTime(s) {
  var b = s.split(/\D/);
  return new Date(b[0], b[1]-1, b[2], b[3], b[4], b[5]);
}


function workCtrl ($scope, $routeParams,$route,DatabaseService, LoginService, $filter, ngDialog, orderBy, $log, $location,$anchorScroll,$timeout) {
    $scope.work={};
    $scope.workers=[];
    $scope.workadmin={};
    var dberr=function(err) {
	$log.debug("db init err "+err);
	if (err['error']) {
	    alert('DB fejl '+err['error']);
	}
    }

  LoginService.check_user().promise.then(function(u) {
    $scope.current_user=u;
  });

  var dbready=function (ok) {
    $scope.worktasks=DatabaseService.getDB('event/worktasks');
    $scope.workers=DatabaseService.getDB('event/workers');
    $scope.work_today=DatabaseService.getDB('event/work_today');
    for (var ti=0; ti<$scope.work_today.length;ti++) {
      var td=$scope.work_today[ti];
      td.start_time=new Date(td.start_time);
      td.start_time.setSeconds(0);
      if (td.end_time) {
        td.end_time=new Date(td.end_time);
        td.end_time.setSeconds(0);
      }
    }
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
          return (preselectedids === undefined || !(element.id in preselectedids)) && element.worker_id==val;
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
        $scope.work.start_time=wd;
        $scope.work.end_time=null;
        $scope.work.hours=null;
        $scope.work.open=true;
        $scope.work.name=$scope.work.worker.name;
        $scope.work.worker_id=$scope.work.worker.worker_id;
        $scope.work.worker.start_time=$scope.work.start_time;
        $scope.work_today.push($scope.work);
        $scope.work={};
      } else {
        $scope.work={};
        alert(status.error);
      }
    }, function(err) {console.log("forum mem add work err: "+err)}
                   )
  }

  $scope.show_worker = function () {
    console.log("ww");
    DatabaseService.getDataNow('event/stats/worker',"worker="+$scope.work.selectedworker.worker_id,function (res) {
      $scope.mystatswork=res.data;
    }
                              );
  }
  
  $scope.end_work = function (work) {
    work.end_time=new Date();
    work.hours=(new Date(work.end_time)-new Date(work.start_time))/3600/1000;
    work.open=false;
    var sr=DatabaseService.createSubmit("update_work",work);
    sr.promise.then(function(status) {
      if (status.status =='ok') {
        $log.debug("worker");
      } else {
        alert(status.error);
      }
    }, function(err) {console.log("forum mem add work err: "+err)}
                   )
  }

  $scope.get_report = function (report) {
    console.log("getreport "+report);
    DatabaseService.getDataNow('event/stats/workstats','format=tablejson&q='+report,
                               function (res) {
        $scope.workreport=res.data;
      },dberr
                                )
  };

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
