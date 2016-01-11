'use strict';
angular.module('myApp.database.database-services', []).service('DatabaseService', function($http, $q, AccessToken) {
  var boats;
  var boatsA; // Real array
  var boattypes;
  var boatcategories;
  var boatdamages;
  var boatdamages_flat;
  var destinations;
  var triptypes;
  var rowers;
  var rowerstatistics={'rowboat':[],'kayak':undefined,'any':undefined};
  var boatstatistics={};
  var databasesource=dbmode;

  var datastatus={
    'boat':null,
    'trip':null,
    'member':null
  };
  function toURL(service){
    if (databasesource=='real') {
      return '../../backend/'+service;
    } else {
      return 'data/'+service.replace('.php','').replace(/\?/g,'Q').replace(/=/g,'')+".json";
    }
  }
  
  this.onDBerror = function (err) {
    alert(err);
  };

  this.init = function () {
    var boatsloaded = $q.defer();
    var boattypesloaded = $q.defer();
    var boatdamagesloaded = $q.defer();
    var destinationsloaded = $q.defer();
    var triptypesloaded = $q.defer();
    var rowersloaded = $q.defer();
    var boatstatisticsloaded = {'any':$q.defer(),'rowboat':$q.defer(),'kayak':$q.defer()};
    var rowerstatisticsloaded = {'any':$q.defer(),'rowboat':$q.defer(),'kayak':$q.defer()};
    var boatmaintypes = ['kayak','any','rowboat'];
    if(boats === undefined || datastatus['boat']===undefined) {
      //Build indexes and lists for use by API
      var headers = {};
      var accessToken = AccessToken.get();
      if (accessToken) {
	  headers['Authorization'] = 'Bearer ' + accessToken.access_token;
      }
      $http.get(toURL('boat_status.php'), { headers: headers } ).then(function(response) {
        boats = {};
	boatsA =[];
        angular.forEach(response.data, function(boat, index) {
          this[boat.id] = boat;
	  boatsA.push(boat);
        }, boats);
        boatcategories = {};
        angular.forEach(response.data, function(boat, index) {
          var category = boat.category;
          if(this[category] === undefined) {
            this[category] = [];
          }
          this[category].push(boat);
        }, boatcategories);
       boatsloaded.resolve(true);
      });

    } else {
      boatsloaded.resolve(true);
    }
    
    if(boatdamages === undefined || boatdamages_flat === undefined || datastatus['boat']===undefined) {
      $http.get(toURL('boatdamages.php')).then(function(response) {
        boatdamages = {};
	boatdamages_flat = response.data;
        angular.forEach(boatdamages_flat, function(boatdamage, index) {
           if(this[boatdamage.boat_id] === undefined) {
            this[boatdamage.boat_id] = [];
          }
          this[boatdamage.boat_id].push(boatdamage);
        }, boatdamages);
        boatdamagesloaded.resolve(true);
      });

    } else {
      boatdamagesloaded.resolve(true);
    }

    if(destinations === undefined) {
      $http.get(toURL('destinations.php')).then(function(response) {
        destinations = response.data;
        destinationsloaded.resolve(true);
      });
    } else {
      destinationsloaded.resolve(true);
    }

    if(boattypes === undefined) {
      $http.get(toURL('boattypes.php')).then(function(response) {
        boattypes = response.data;
        boattypesloaded.resolve(true);
      });
    } else {
      boattypesloaded.resolve(true);
    }

    
    if(triptypes === undefined) {
      $http.get(toURL('triptypes.php')).then(function(response) {
        triptypes = response.data;
        triptypesloaded.resolve(true);
      });
    } else {
      triptypesloaded.resolve(true);
    }

    if(rowers === undefined) {
      $http.get(toURL('rowers.php')).then(function(response) {
        rowers = [];
        angular.forEach(response.data, function(rower, index) {
          rower.search = (rower.id + " " + rower.name).toLocaleLowerCase();
          this.push(rower);
        }, rowers);
        rowersloaded.resolve(true);
      });
    } else {
      rowersloaded.resolve(true);
    }
      
    if(rowerstatistics['any'] === undefined) {
      var bx;
      for (bx in boatmaintypes) {
	(function(boattype) {
	  //var farg="?noop=42";
	  // FIXME for test purposes
	  var farg="?season=2014";
	  if (boattype != "any") {
	    farg+='&boattype='+boattype;
	    	   // farg='Qboattype'+boattype;
	  }
	  $http.get(toURL('rower_statistics.php'+farg)).then(function(response) {
            rowerstatistics[boattype] = [];
            angular.forEach(response.data, function(stat, index) {
              //stat.search = stat.id + " " + stat.firstname + " " + stat.lastname;
              this.push(stat);
            }, rowerstatistics[boattype]);
	    rowerstatisticsloaded[boattype].resolve(true);
	  });
	})(boatmaintypes[bx]);
      }
    } else {
      rowerstatisticsloaded['any'].resolve(true);
      rowerstatisticsloaded['rowboat'].resolve(true);
      rowerstatisticsloaded['kayak'].resolve(true);
    }
    
    var qll=$q.all([boatsloaded.promise,boattypesloaded.promise,
		    boatdamagesloaded.promise, destinationsloaded.promise, 
		    triptypesloaded.promise, rowersloaded.promise,rowerstatisticsloaded['any'].promise,rowerstatisticsloaded['kayak'].promise,rowerstatisticsloaded['rowboat'].promise]);
    return qll;
  };

  this.defaultLocation = 'DSR';
  this.sync=function() {
    $http.post('../../backend/datastatus.php', data).success(function(ds, status, headers, config) {
      var doreload=false;
      for (tp in ds) {
	if (datastatus[tp]!=ds[tp]) {
	  doreload=true;
	  if (tp=='boat'){
	    boats=null;
	    boatdamages=null;
	  } else if (tp=='trip') {
	    boatstatistics=null;
	    rowerstatistics['any']=null;
	  } else if (tp=='member') {
	    boatstatistics=null;
	    rowers=null;
	  }
	}
	datastatus[tp]=ds[tp];
      }
      if (doreload) {
	this.init();
      }
    });
  }
  
  this.reload=function (invalidate) {
    datastatus['boat']=undefined;
    this.init();
  }

    this.getBoatTypes = function () {
    return boattypes;
  };

  this.getBoatWithId = function (boat_id) {
    return boats[boat_id];
  };

  this.getBoats = function () {
    return boatsA;
  };

  this.getBoatStatuses = function (boat_id) {
    // On the water(Checkouted), Being booked(Locked until), Reserved, Has damage(Severe, Medium, Light) = ?
  };
  
  this.lockBoatWithId = function (boat_id, date) {
    var timestamp = date.toISOString();
    // TODO: Send timestamp to server
    console.log("Lock "+ boat_id + " : " + timestamp);
  };
  
  this.getDamagesWithBoatId = function (boat_id) {
    return boatdamages[boat_id];
  };

  this.getDamages = function () {    
    return boatdamages_flat;
  };

  this.getBoatsWithCategoryName = function (categoryname) {
    var boats = boatcategories[categoryname];
    if (boats) {
      return boats.sort(function (a, b) {
        return a.name.localeCompare(b.name);
      });
    } else {
      return null;
    }
  };
  
  this.nameSearch = function (list,name) {
    for (var i=0;i<list.length;i++) {
      if (list[i].name==name) return list[i];
    }
    return null;
  }

  this.getDestinations = function (location) {
    var loc='DSR';
    if(location !== undefined) {
      loc=location;
    }
      return destinations[loc];
  };
  
  this.getTripTypes = function () {
    return triptypes;
  };

  this.getOnWater = function (onSuccess) {
    $http.get(toURL('onwater.php')).then(onSuccess);
  }
  this.getTodaysTrips = function (onSuccess) {
    $http.get(toURL('tripstoday.php')).then(onSuccess);
  }
  this.getAvailableBoats = function (location,onSuccess) {
    $http.get(toURL('availableboats.php?location='+location)).then(onSuccess);
  }

  this.getRowerTripsAggregated = function (member,onSuccess) {
    $http.get(toURL('rowertripsaggregated.php?member='+member.id)).then(onSuccess);
  }
  this.getRowerTrips = function (member,onSuccess) {
    $http.get(toURL('rowertrips.php?member='+member.id)).then(onSuccess);
  }
  this.getTripMembers = function (tripid,onSuccess) {
    $http.get(toURL('tripmembers.php?trip='+tripid)).then(onSuccess,this.onDBerror);
  }  
  this.getRowerStatistics = function (bt) {
    return rowerstatistics[bt];
  };
  this.getBoatStatistics = function (bt) {
    return boatstatistics[bt];
  };

  this.getRower = function(val) {
    var rs=rowers.filter(function(element) {
      return element['id']==val;
    });
    return rs[0];
  }
    
  this.getRowersByNameOrId = function(nameorid, preselectedids) {
    var val = nameorid.toLowerCase();
    var result = rowers.filter(function(element) {
      return (preselectedids === undefined || !(element.id in preselectedids)) && element['search'].indexOf(val) > -1;
    });
    return result;
  };
  
  this.createRowerByName = function(name) {
    // TODO: implement
    return {
        "id": "K1",
        "name": name
      };
  };
  
  this.closeTrip = function(data) {
    var tripClosed=$q.defer();
    var res=undefined;
    $http.post('../../backend/closetrip.php', data).success(function(sdata,status,headers,config) {
      tripClosed.resolve(sdata);
      // TODO: make sure we block until the trip is created    
    }).error(function(sdata,status,headers,config) {
      tripClosed.resolve(false);
      // TODO: make sure we block until the trip is created    
    });
    datastatus['trip']=null;
    return tripClosed;
  };


  this.createTrip = function(data) {
    var tripCreated=$q.defer();
    var res=undefined;
    $http.post('../../backend/createtrip.php', data).success(function(sdata,status,headers,config) {
      tripCreated.resolve(sdata);
      // TODO: make sure we block until the trip is created    
    }).error(function(sdata,status,headers,config) {
      tripCreated.resolve(false);
      // TODO: make sure we block until the trip is created    
    });
    datastatus['trip']=null;
    return tripCreated;
  };

  this.newDamage = function(data) {
    $http.post('../../backend/newdamage.php', data).success(function(data, status, headers, config) {
    }).error(function(data, status, headers, config) {
      alert("det mislykkedes at tilføje ny skade "+status+" "+data);
    });
    boatdamages=null;
    return 1;
  };

  this.fixDamage = function(data) {
    $http.post('../../backend/fixdamage.php', data).success(function(data, status, headers, config) {
    }).error(function(data, status, headers, config) {
      alert("det mislykkedes at klarmelde skade "+status+" "+data);
    });
    boatdamages=null;
    return 1;
  };

});
