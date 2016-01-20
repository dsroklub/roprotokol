'use strict';


// TODO Edit destinations
// TODO Edit boat categories
// TODO Edit boats
// TODO edit rower rigths
// TODO edit triptypes required rights
// TODO edit boat category required rights

app.controller('AdminCtrl', ['$scope', 'DatabaseService', 'NgTableParams', '$filter',
                             function ($scope,   DatabaseService,   NgTableParams, $filter) {
          DatabaseService.init().then(function () {
            $scope.currentrower=null;            
            $scope.DB=DatabaseService.getDB;
            $scope.allboats = DatabaseService.getBoats();
            $scope.locations = DatabaseService.getDB('locations');
            $scope.boatkayakcategories = DatabaseService.getDB('boatkayakcategory');
            var errortrips = DatabaseService.getDB('errortrips');
            $scope.ziperrors=[];
            var i=0;
            while (i < errortrips.length -1) {
              console.log(" i="+i);
              while (errortrips[i].id && i < errortrips.length-1) {
                i++;
              }
              var j=i+1;
              while (j<errortrips.length && errortrips[j].Trip==errortrips[i].Trip) {
                $scope.ziperrors.push({
                  'trip':errortrips[i].Trip,
                  'current':errortrips[i],
                  'correction':errortrips[j]
                });
                j++;
              }
              i++;
            }
            $scope.boatcategories = DatabaseService.getBoatTypes();
            
            $scope.getRowerByName = function (val) {
              return DatabaseService.getRowersByNameOrId(val, undefined);
            };
            
            $scope.boatcat2dk=DatabaseService.boatcat2dk;
            $scope.rightsubjects=['cox','all','any','none'];

            $scope.doboatrights = function (rr,bt){
              $scope.requiredrights=rr;
              $scope.currentboattype=bt;
            }
            $scope.dotriprights = function (rr,tt){
              $scope.requiredtriprights=rr;
              $scope.currenttriptype=tt;
            }

            
          }
                                     )
        }
                            ]
          )


