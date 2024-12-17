'use strict';
angular.module('gymApp').controller(
  'teamCtrl',
  ['$scope', '$routeParams', 'DatabaseService', '$filter', 'ngDialog','$log','$timeout',teamCtrl]);

function teamCtrl ($scope, $routeParams, DatabaseService, $filter, ngDialog, $log,  $timeout) {
  $scope.newteam={dayofweek:"Mandag"};
  $scope.attendance = [];
  $scope.dayofweek=99;
  $scope.quarters = [1,2,3,4];
  $scope.todpattern="[0-2]\\d:[0-5]\\d";
  $scope.weekdays=["Mandag","Tirsdag","Onsdag","Torsdag","Fredag","Lørdag","Søndag"];
  $scope.currentdate=new Date();

  DatabaseService.init({"gym":true,"member":true}).then(function () {
    $scope.teams = DatabaseService.getDB('team/team');
    $scope.attendance=DatabaseService.getDB("team/attendance");
    $scope.currentdate=new Date();
  });

  var refreshDOW = function() {
    var dow=new Date().getDay();
    if (dow==0) dow=7;
    if (dow != $scope.dayofweek) {
      $scope.dayofweek = dow;
    }
    $timeout(refreshDOW, 3600000);  // once an hour
  };
  refreshDOW();


  $scope.isSameDay= function() {
    var d=new Date();
    return (d.getDate()==$scope.currentdate.getDate() &&
            d.getMonth()==$scope.currentdate.getMonth() &&
            d.getYear()==$scope.currentdate.getYear());
  }


  $scope.getRowerByName = function (val) {
    return DatabaseService.getRowersByNameOrId(val, $scope.attendance,$scope.currentteam);
  };

  $scope.setCurrentTeam = function (tm) {
    if (tm.today>0) {
      $scope.currentteam=tm;
    }
  }

  $scope.setTeam = function (tm) {
    if (!$scope.isSameDay()) {
      DatabaseService.init({"team":true,"member":true}).then(function () {
        $scope.currentdate=new Date();
        $scope.setCurrentTeam(tm);
      });
    } else {
      $scope.setCurrentTeam(tm);
    }
  }

  $scope.addTeam = function() {
    $log.debug("add team");
    DatabaseService.addTeam($scope.newteam).promise.then(
      function(st) {
        if (st.status=="ok") {
          $scope.newteam['today']=($scope.newteam['dayofweek']==$scope.weekdays[new Date().getDay()-1]);
          $scope.teams.push($scope.newteam);
        }
      }
    );
  }

  $scope.deleteTeam = function(tm) {
    $log.debug("delete team");
    DatabaseService.deleteTeam(tm).promise.then(
      function(st) {
        if (st.status=="ok") {
          $scope.teams.splice($scope.teams.indexOf(tm),1);
        }
      }
    );
  }

  $scope.attend = function() {
    if ($scope.currentteam && $scope.attendee  && $scope.attendee.id) {
      $scope.checkout = {
        'member' : $scope.attendee,
        'team' : $scope.currentteam,
        'destination': {'distance':999},
        'comments':''
      }

      DatabaseService.attendTeam($scope.checkout).promise.then(
        function(st) {
          if (st.status=="ok") {
            $scope.attendance.splice(0,0, {'team': $scope.currentteam.name, membername:$scope.attendee.name,
                                           memberid:$scope.attendee.id,
                                           dayofweek:$scope.currentteam.dayofweek,
                                           timeofday:$scope.currentteam.timeofday
                                          });
            $scope.attendee=null;
          } else if (st.message && st.message.search("Duplicate entry")) {
            $scope.message="Allerede tilmeldt";
          }
        }
      )
    } else {
      if ($scope.attendee  && !$scope.attendee.id) {
        $scope.attendee=null;
      }
    }
  }
}
