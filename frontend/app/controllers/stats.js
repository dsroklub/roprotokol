'use strict';

app.controller('StatCtrl', ['$scope', '$routeParams', 'DatabaseService', '$interval', 'ngDialog', function ($scope, $routeParams, DatabaseService, $interval, ngDialog) {
    DatabaseService.init().then(function () {      
      $scope.stats = DatabaseService.getStatistics();
    });    
    $scope.isObjectAndHasId = function (val) {
      return typeof(val) === 'string' && val.length > 3;
    };  
}]);

