'use strict';

coxApp.controller(
  'menuCtrl',  ['$scope', '$location', '$route',
                function ($scope, $location,$route ) {
                  $scope.activePath = null;
                  $scope.$on('$routeChangeSuccess', function(){
                    $scope.activePath = $location.path();
                    // $scope.activePath = "cox.html";
                    // console.log( $location.path() );
                  });
                }
               ]
)

