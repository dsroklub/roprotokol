'use strict';

gymApp.controller(
  'menuCtrl',  ['$scope', '$location', '$route','LoginService',
                function ($scope,   $location,$route,LoginService ) {
                  $scope.activePath = null;
                  $scope.showadmin=false;
                  LoginService.check_user().promise.then(function(u) {         
                    $scope.currentuser=u.member_id;
                    if (u.has_remote_access) {
                      $scope.showadmin=true;
                    }
                  });

                  $scope.$on('$routeChangeSuccess', function(){
                    $scope.activePath = $location.path();
                    console.log( $location.path() );
                  });
                }
               ]
)

