'use strict';

gymApp.controller(
  'menuCtrl',  ['$scope', '$location', '$route',
                function ($scope,   $location,$route ) {
                  $scope.activePath = null;
                  $scope.$on('$routeChangeSuccess', function(){
                    $scope.activePath = $location.path();
                    console.log( $location.path() );
                  });
                }
               ]
)

