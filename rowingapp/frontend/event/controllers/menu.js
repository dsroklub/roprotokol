'use strict';

eventApp.controller(
  'menuCtrl',  ['$scope', '$location', '$route',
                function ($scope, $location,$route ) {
                  $scope.activePath = null;
                  $scope.$on('$routeChangeSuccess', function(){
                    $scope.activePath = $location.path();
                    console.log("route change: "+ $location.path());
                  });
                }
               ]
)

