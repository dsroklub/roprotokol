'use strict';

angular.module('rowApp').controller(
  'menuCtrl',  ['$scope', '$location', '$route', 'StatusService','$log',menuCtrl]
);

function menuCtrl ($scope, $location,$route,StatusService,$log) {
  $scope.activePath = null;
  StatusService.publiccurrentuser().then(function onSuccess(r) {
    $scope.userStatus=r.data;
  }
  )
  $scope.$on('$routeChangeSuccess', function(){
    $scope.activePath = $location.path();
  });
}

