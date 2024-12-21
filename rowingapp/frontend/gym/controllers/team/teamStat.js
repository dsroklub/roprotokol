'use strict';
angular.module('gymApp').controller(
  'teamStatCtrl',
  ['$scope', '$routeParams', 'DatabaseService', '$filter', 'ngDialog','$log','$timeout',teamStatCtrl]);

function teamStatCtrl ($scope, $routeParams, DatabaseService, $filter, ngDialog, $log,  $timeout) {
  $scope.teamNames=[{"name":"foo"}];
  $scope.teamstats=[];
  $scope.selectedTeam="alle";
  $scope.selectedGymnast=null;
  $scope.deltagerStats=null
  DatabaseService.init({"gym":true,"member":true}).then(function () {
    $scope.teamNames = DatabaseService.getDB('team/teamNames');
    $scope.teamStats = DatabaseService.getDB('team/teamStats');
    $scope.currentdate=new Date();
  });

  $scope.selectGymnast = function () {
    $scope.deltagerStats=null;
    DatabaseService.getDataNow('team/gymnastStat','memberid='+$scope.selectedGymnast.id, function (res) {
      $scope.deltagerStats=res.data;
    });
  }

  $scope.getRowerByName = function (val) {
    return DatabaseService.getRowersByNameOrId(val, $scope.selectedGymnast,$scope.currentteam);
  };

}
