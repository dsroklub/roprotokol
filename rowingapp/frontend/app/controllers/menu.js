'use strict';

angular.module('rowApp').controller(
  'menuCtrl',  ['$scope', '$location', '$route', menuCtrl]
);

function menuCtrl ($scope, $location,$route) {
  $scope.activePath = null;
  $scope.$on('$routeChangeSuccess', function(){
    $scope.activePath = $location.path();
  });
}

