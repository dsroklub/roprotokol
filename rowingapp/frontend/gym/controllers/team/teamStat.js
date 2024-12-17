'use strict';
angular.module('gymApp').controller(
  'teamStatCtrl',
  ['$scope', '$routeParams', 'DatabaseService', '$filter', 'ngDialog','$log','$timeout',teamStatCtrl]);
console.log("teamstat");

function teamStatCtrl ($scope, $routeParams, DatabaseService, $filter, ngDialog, $log,  $timeout) {
  $scope.teamNames=[{"name":"foo"}];
  $scope.teamstats=[];
  $scope.selectedTeam="alle";
  DatabaseService.init({"gym":true,"member":true}).then(function () {
    $scope.teamNames = DatabaseService.getDB('team/teamNames');
    $scope.teamStats = DatabaseService.getDB('team/teamStats');
    $scope.currentdate=new Date();
  });
}
