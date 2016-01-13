'use strict';
angular.module('myApp.database.database-services', []).service('DatabaseService', function($http, $q, AccessToken) {
  var valid={};
  var db={};
  var rowerstatistics={'rowboat':[],'kayak':undefined,'any':undefined};
  var boatstatistics={};
  var databasesource=dbmode;
  var tx=null;
  
  var cachedepend={
    'boat':['boats','boatdamages'],
    'trip':['rowers','rowerstatisticsany','rowerstatisticsanykayak','rowerstatisticsanyrowboat'],
    'member':[]
  };
  
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
    var boatmaintypes = ['kayak','any','rowboat'];

    var headers = {};
    var accessToken = AccessToken.get();
    var promises=[];
    if (accessToken) {
      headers['Authorization'] = 'Bearer ' + accessToken.access_token;
    }

    if(!valid['boats']) {
      //Build indexes and lists for use by API
      var bq=$q.defer();
      promises.push(bq.promise);
      $http.get(toURL('boat_status.php'), { headers: headers } ).then(function(response) {
        db['boats'] = {};
	db['boatsA'] =[];
        angular.forEach(response.data, function(boat, index) {
          this[boat.id] = boat;
	  db['boatsA'].push(boat);
        }, db['boats']);
        db['boatcategories'] = {};
        angular.forEach(response.data, function(boat, index) {
          var category = boat.category;
          if(this[category] === undefined) {
            this[category] = [];
          }
          this[category].push(boat);
        }, db['boatcategories']);
	valid['boats']=true;
	bq.resolve(true);
      });
    } 
    
    if (!valid['boatdamages']) {
      var bdq=$q.defer();
      promises.push(bdq.promise);
      $http.get(toURL('boatdamages.php')).then(function(response) {
        db['boatdamages'] = {};
	db['boatdamages_flat'] = response.data;
        angular.forEach(db['boatdamages_flat'], function(boatdamage, index) {
           if(this[boatdamage.boat_id] === undefined) {
            this[boatdamage.boat_id] = [];
          }
          this[boatdamage.boat_id].push(boatdamage);
        }, db['boatdamages']);
	valid['boatdamages']=true;
        bdq.resolve(true);
      });
    } 

    if(!valid['destination']) {
      var dq=$q.defer();
      promises.push(dq.promise);
      $http.get(toURL('destinations.php')).then(function(response) {
        db['destinations'] = response.data;
	valid['destination']=true;
        dq.resolve(true);
      });
    }

    if(!valid['boattypes']) {
      var btq=$q.defer();
      promises.push(btq.promise);
      $http.get(toURL('boattypes.php')).then(function(response) {
        db['boattypes'] = response.data;
	valid['boattypes']=true;
        btq.resolve(true);
      });
    }

    
    if(!valid['triptypes']) {
      var bttq=$q.defer();
      promises.push(bttq.promise);
      $http.get(toURL('triptypes.php')).then(function(response) {
        db['triptypes'] = response.data;
	valid['triptypes']=true;
        bttq.resolve(true);
      });
    }

    if(!valid['rowers']) {
      var rq=$q.defer();
      promises.push(rq.promise);
      $http.get(toURL('rowers.php')).then(function(response) {
        db['rowers'] = [];
        angular.forEach(response.data, function(rower, index) {
          rower.search = (rower.id + " " + rower.name).toLocaleLowerCase();
          this.push(rower);
        }, db['rowers']);
	valid['rowers']=true;
        rq.resolve(true);
      });
    }
      
    for (var bi=0; bi<boatmaintypes.length; bi++) {
      var boattype= boatmaintypes[bi];
      if(!valid['rowerstatistics'+boattype]) {
	(function (bt) {
	  var sq=$q.defer();
	  promises.push(sq.promise);
	  // FIXME for test purposes
	  var farg="?season=2014";
	  if (bt != "any") {
	    farg+='&boattype='+bt;
	  }      
	  $http.get(toURL('rower_statistics.php'+farg)).then(function(response) {
            rowerstatistics[bt] = [];
            angular.forEach(response.data, function(stat, index) {
              //stat.search = stat.id + " " + stat.firstname + " " + stat.lastname;
              this.push(stat);
            }, rowerstatistics[bt]);
	    valid['rowerstatistics'+boattype]=true;	  
	    sq.resolve(true);
	  });
	})(boattype);
      } 
    }
    
    var qll=$q.all(promises);
    tx=qll;
    return qll;
  };

  this.defaultLocation = 'DSR';

  this.sync=function() {
    var dbservice=this;
    $http.post('../../backend/datastatus.php', null).success(function(ds, status, headers, config) {
      var doreload=false;
      console.log("do db sync");
      for (var tp in ds) {
	if (datastatus[tp]!=ds[tp]) {
	  doreload=true;
	  console.log("  dirty: "+tp);
	  for (var di=0;cachedepend[tp] && di < cachedepend[tp].length;di++) {
	    var subtp=cachedepend[tp][di];
	    console.log("    invalidate: "+subtp);
	      valid[subtp]=false;	    
	  }
	}
	datastatus[tp]=ds[tp];
      }
      if (doreload) {
	dbservice.init();
      }
    });
  }
  
  this.reload=function (invalidate) {
    datastatus['boat']=undefined;
    this.init();
  }

    this.getBoatTypes = function () {
    return db['boattypes'];
  };

  this.getBoatWithId = function (boat_id) {
    return (db['boats'])[boat_id];
  };

  this.getBoats = function () {
    return db['boatsA'];
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
    return db['boatdamages'][boat_id];
  };

  this.getDamages = function () {    
    return db['boatdamages_flat'];
  };

  this.getBoatsWithCategoryName = function (categoryname) {
    db['boats'] = db['boatcategories'][categoryname];
    if (db['boats']) {
      return db['boats'].sort(function (a, b) {
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
      return db['destinations'][loc];
  };
  
  this.getTripTypes = function () {
    return db['triptypes'];
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
    var rs=db['rowers'].filter(function(element) {
      return element['id']==val;
    });
    return rs[0];
  }
    
  this.getRowersByNameOrId = function(nameorid, preselectedids) {
    var val = nameorid.toLowerCase();
    var result = db['rowers'].filter(function(element) {
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
    valid['boat']=false;
    return 1;
  };

  this.fixDamage = function(data) {
    $http.post('../../backend/fixdamage.php', data).success(function(data, status, headers, config) {
    }).error(function(data, status, headers, config) {
      alert("det mislykkedes at klarmelde skade "+status+" "+data);
    });
    valid['boat']=false;
    return 1;
  };

  this.test = function(src) {
    var boats = db['boatcategories']["Inrigger 2+"];
    boats[1].trip=4242;
  }

});
