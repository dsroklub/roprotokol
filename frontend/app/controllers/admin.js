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
            $scope.memberrighttypes = DatabaseService.getDB('memberrighttypes');
            $scope.boatkayakcategories = DatabaseService.getDB('boatkayakcategory');
            var errortrips = DatabaseService.getDB('errortrips');
            $scope.ziperrors=[];
            var i=0;
            while (i < errortrips.length -1) {
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

            $scope.setboatkayak = function(bt) {
              var exeres=DatabaseService.updateDB('setboatkayak',bt);
            }

            $scope.create_boattype = function(bt) {
              console.log("net boattype");
              var exeres=DatabaseService.updateDB('create_boattype',bt);
            }

            $scope.set_duration = function(destination,loc) {
              var exeres=DatabaseService.updateDB('set_duration',destination);
            }
            $scope.set_distance = function(destination,loc) {
              var exeres=DatabaseService.updateDB('set_distance',destination);
            }

            $scope.set_cat_for_boat = function(boat) {
              var exeres=DatabaseService.updateDB('set_cat_for_boat',boat);
            }
            $scope.set_loc_for_boat = function(boat) {
              var exeres=DatabaseService.updateDB('set_loc_for_boat',boat);
            }
            $scope.retire_boat = function(boat,ix) {
              var exeres=DatabaseService.updateDB('retire_boat',boat);
              $scope.allboats.splice(ix,1);              
            }
            $scope.create_boat = function(boat) {
              var exeres=DatabaseService.updateDB('create_boat',boat);
            }


            $scope.add_rower_right = function(right,rower) {
              var data={'right':right,'rower':rower}
              var exeres=DatabaseService.updateDB('add_rower_right',data);
              $scope.currentrower.rights[right.member_right]=Date();
            }
            $scope.remove_rower_right = function(rower,right) {             
              var data={'right':right,'rower':rower}
              var exeres=DatabaseService.updateDB('remove_rower_right',data);
              delete $scope.currentrower.rights[right];
            }
            
            $scope.remove_boattype_requirement = function(rt,ix) {
              var data={boattype:$scope.currentboattype,'right':rt};
              var exeres=DatabaseService.updateDB('remove_boattype_right',data);
              delete $scope.requiredrights[rt];

            }

            $scope.add_boattype_requirement = function(data) {
              data.boattype=$scope.currentboattype;
              $scope.requiredrights[data.right]=data.subject;
              var exeres=DatabaseService.updateDB('add_boattype_req',data);
            }


            $scope.remove_triptype_requirement = function(rt,ix) {
              var data={triptype:$scope.currenttriptype,'right':rt};
              var exeres=DatabaseService.updateDB('remove_triptype_req',data);
              delete $scope.requiredtriprights[rt];

            }
            $scope.add_triptype_requirement = function(data) {
              data.triptype=$scope.currenttriptype;
              $scope.requiredtriprights[data.right]=data.subject;
              var exeres=DatabaseService.updateDB('add_triptype_req',data);
            }

            $scope.approve_correction = function(data,ix) {
              var exeres=DatabaseService.updateDB('approve_correction',data);
              $scope.ziperrors.splice(ix,1);                            
            }
            $scope.reject_correction = function(data,ix) {
              var exeres=DatabaseService.updateDB('reject_correction',data);
              $scope.ziperrors.splice(ix,1);              
            }
            
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


