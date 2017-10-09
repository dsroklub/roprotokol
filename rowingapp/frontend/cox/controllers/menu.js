'use strict';

coxApp.controller(
  'menuCtrl',  ['$scope', '$location', '$route','LoginService',
                function ($scope, $location,$route,LoginService ) {
                  $scope.activePath = null;
                  $scope.currentuser=LoginService.get_user();
                  $scope.ccurrentuser=LoginService.get_cuser();
                  $scope.$on('$routeChangeSuccess', function(){
                    $scope.activePath = $location.path();
                  });
                }
               ]
)

