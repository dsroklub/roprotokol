'use strict';


// TODO Edit destinations
// TODO Edit boat categories
// TODO Edit boats
// TODO edit rower rigths
// TODO edit triptypes required rights
// TODO edit boat category required rights

app.controller('AdminCtrl', ['$scope', 'DatabaseService', 'NgTableParams', '$filter', '$route',
                             function ($scope,   DatabaseService, NgTableParams, $filter,$route) {
          DatabaseService.init().then(function () {
            $scope.currentrower=null;            
            $scope.DB=DatabaseService.getDB;
            $scope.allboats = DatabaseService.getBoats();
            $scope.locations = DatabaseService.getDB('locations');
            $scope.memberrighttypes = DatabaseService.getDB('memberrighttypes');
            $scope.boatkayakcategories = DatabaseService.getDB('boatkayakcategory');
            var errortrips = DatabaseService.getDB('errortrips');
            $scope.levels =DatabaseService.getDB('boatlevels');
            $scope.brands =DatabaseService.getDB('boat_brand');
            $scope.usages =DatabaseService.getDB('boat_usages');

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

            $scope.errorhandler = function(error) {
              console.log(error);
              $route.reload();
            }
            
            $scope.getRowerByName = function (val) {
              return DatabaseService.getRowersByNameOrId(val, undefined);
            };

            $scope.setboatkayak = function(bt) {
              var exeres=DatabaseService.updateDB('setboatkayak',bt);
            }

            $scope.create_boattype = function(bt) {
              console.log("net boattype");
              var exeres=DatabaseService.updateDB('create_boattype',bt);
              $scope.DB('boattypes').push(bt);
            }

            $scope.update_level = function(boat) {
              var exeres=DatabaseService.updateDB('boat_update_level',boat);
            }
            $scope.update_brand = function(boat) {
              var exeres=DatabaseService.updateDB('boat_update_brand',boat);
            }
            $scope.update_usage = function(boat) {
              var exeres=DatabaseService.updateDB('boat_update_usage',boat);
            }

            $scope.update_usage_name = function(boat) {
              var exeres=DatabaseService.updateDB('usage_update_name',boat);
            }
            $scope.update_usage_description = function(usage) {
              var exeres=DatabaseService.updateDB('usage_update_description',usage);
            }
            $scope.update_usage_name = function(usage) {
              var exeres=DatabaseService.updateDB('usage_update_description',usage);
            }
            $scope.create_usage = function(usage) {
              console.log('create new usage '+usage);
              var exeres=DatabaseService.updateDB_async('usage_create',usage).then(
                function(newusage) {
                  if (newusage.status=="ok") {
                    $scope.usages.push(newusage.newusage);
                  }                  
                }
              );
              usage.name="";
              usage.description="";
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
              $scope.allboats.push(boat);
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
              delete $scope.requiredboatrights[rt];

            }

            $scope.add_boattype_requirement = function(data) {
              data.boattype=$scope.currentboattype;
              var exeres=DatabaseService.updateDB('add_boattype_req',data,$scope.errorhandler);
              $scope.requiredboatrights[data.right]=data.subject;
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
              if (rr&rr.length==0) { // Hack, must be due to PHP json marshalling
                $scope.requiredboatrights={};
              } else {
                $scope.requiredboatrights=rr;
              }
              $scope.currentboattype=bt;
            }
            $scope.dotriprights = function (rr,tt){
              if (rr&rr.length==0) { // Hack, must be due to PHP json marshalling
                $scope.requiredtriprights={};
              } else {
               $scope.requiredtriprights=rr;
              }
              $scope.currenttriptype=tt;
            }

            
          }
                                     )
        }
                            ]
          )


