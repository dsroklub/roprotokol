'use strict';
angular.module('myApp.database.database-services', []).service('DatabaseService', function($http, $q,$log, AccessToken) {
  var valid={};
  var db={};
  var rowerstatistics={'rowboat':[],'kayak':undefined,'any':undefined};
  var boatstatistics={'rowboat':[],'kayak':undefined,'any':undefined};
  var databasesource=dbmode;
  var tx=null;

  this.boatcat2dk = {
      'any':'alle',
      'rowboat':'robåd',
      'kayak':'kajak',
      'kaniner':'kaniner'
  };
  
  var cachedepend={
    'boat':['boats','boatdamages'],
    'trip':['rowers','rowerstatisticsany','rowerstatisticsanykayak','rowerstatisticsanyrowboat', 'boats'],
    'member':['boats']
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

  this.getDB = function (dataid) {
    return db[dataid];
  }

    this.getData = function (dataid,promises) {
    if(!valid[dataid]) {
      var dq=$q.defer();
            promises.push(dq.promise);
      $http.get(toURL(dataid+'.php')).then(function(response) {
        db[dataid] = response.data;
	valid[dataid]=true;
        dq.resolve(dataid);
      });
    }
  }

  this.init = function () {
    var boatmaintypes = ['kayak','any','rowboat'];
    console.log("DB init "+Date());

    var headers = {};
    var accessToken = AccessToken.get();
    var promises=[];
    if (accessToken) {
      headers['Authorization'] = 'Bearer ' + accessToken.access_token;
    }

    if(!valid['boats']) {
      //Build indexes and lists for use by API
      console.log("  boats not valid");
      var bq=$q.defer();
      promises.push(bq.promise);
      $http.get(toURL('boat_status.php'), { headers: headers } ).then(function(response) {
	console.log("    received boat statuses");
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
	console.log("    resolved boat statuses");
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

    this.getData('destinations',promises);
    this.getData('boattypes',promises);
    this.getData('triptypes',promises);

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

      if(!valid['boatstatistics'+boattype]) {
	(function (bt) {
	  var sq=$q.defer();
	  promises.push(sq.promise);
	  // FIXME for test purposes
	  var farg="?season=2014";
	  if (bt != "any") {
	    farg+='&boattype='+bt;
	  }      
	  $http.get(toURL('boat_statistics.php'+farg)).then(function(response) {
            boatstatistics[bt] = [];
            angular.forEach(response.data, function(stat, index) {
              this.push(stat);
            }, boatstatistics[bt]);
	    valid['boatstatistics'+boattype]=true;	  
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

  this.invalidate_dependencies=function(tp) {
    console.log("  dirty: "+tp);
    for (var di=0;cachedepend[tp] && di < cachedepend[tp].length;di++) {
      var subtp=cachedepend[tp][di];
      console.log("    invalidate: "+subtp);
      valid[subtp]=false;	    
    }
  }
  
  this.sync=function() {
    var dbservice=this;
    var sq=$q.defer();
    $http.post('../../backend/datastatus.php', null).success(function(ds, status, headers, config) {
      var doreload=false;
      console.log("do db sync");
      for (var tp in ds) {
	if (datastatus[tp]!=ds[tp]) {
	  dbservice.invalidate_dependencies(tp);
	  doreload=true;
	}
	datastatus[tp]=ds[tp];
      }
      if (doreload) {
	console.log(" do reload " + JSON.stringify(valid));
	dbservice.init().then(function() {
	  sq.resolve("sync done");
	});
      } else {
	sq.resolve("nothing to do");
      }
    });
    return sq.promise;
  }
  
  this.reload=function (tps) {
    for (var ti=0; ti<tps.length; ti++) {
      console.log('reload '+tps[ti])
      this.invalidate_dependencies(tps[ti]);
    }
    this.init();
  }

  this.getBoatTypes = function () {
    return db['boattypes'];
  };

  this.getBoatTypeWithName = function (name) {
    $log.debug("FOO"+name);
    for (var i=0;i<db['boattypes'].length;i++) {
      $log.debug(" F"+db['boattypes'][i].name);
      if (db['boattypes'][i].name==name) {
	$log.debug("found "+db['boattypes'][i].name);
	return (db['boattypes'][i]);
      }
    }
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
  
  this.getDamagesWithBoatId = function (boat_id) {
    return db['boatdamages'][boat_id];
  };

  this.getDamages = function () {    
    return db['boatdamages_flat'];
  };

  this.getBoatsWithCategoryName = function (categoryname) {
    var boats = db['boatcategories'][categoryname];
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
      return db['destinations'][loc];
  };


  this.getDestinationWithName = function(name,location) {
    for (var i=0; i<this.getDestinations(location).length;i++) {
      var dc=this.getDestinations(location)[i];
      if (angular.equals(dc.name,name)) {
	return dc;
      }
    }
  }

  this.getTriptypeWithID = function(tid) {
    for (var i=0; i<db['triptypes'].length;i++) {
      if (db['triptypes'][i].id==tid) {
	return db['triptypes'][i];
      }
    }
  }

  this.getTripTypes = function () {
    return db['triptypes'];
  };

  this.getDataNow = function(dataid,arg,onSuccess) {
    var a="";
    if (arg) {
      a="?"+arg;
    }
    $http.get(toURL(dataid+'.php'+a)).then(onSuccess);
  }
  
  this.getOnWater = function (onSuccess) {
    this.getDataNow('onwater',null,onSuccess);
  }

  this.getTodaysTrips = function (onSuccess) {
    this.getDataNow('tripstoday',null,onSuccess);
  }
  this.getAvailableBoats = function (location,onSuccess) {
    this.getDataNow('availableboats','location='+location,onSuccess);
  }

  this.getRowerTripsAggregated = function (member,onSuccess) {
    this.getDataNow('rowertripsaggregated','member='+member.id,onSuccess);
  }
  this.getRowerTrips = function (member,onSuccess) {
    this.getDataNow('rowertrips','member='+member.id,onSuccess);
  }
  this.getTripMembers = function (tripid,onSuccess) {
    this.getDataNow('tripmembers','trip='+tripid,onSuccess);
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
    var first;
    var last;
    return {
        "id": "K1",
      "first": first,
      "lastt": last
      };
  };
  
  this.closeForm = function(form,data,datakind) {
    var formClosed=$q.defer();
    var res=undefined;
    $http.post('../../backend/'+form+'.php', data).success(function(sdata,status,headers,config) {
      formClosed.resolve(sdata);
    }).error(function(sdata,status,headers,config) {
      formClosed.resolve(false);
    });
    datastatus[datakind]=null;
    return formClosed;
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

  this.mergeArray = function (array1,array2) {
    var ra={}
    if (array1) 
      for(var item in array1) {
	ra[item] = array1[item];
      }
    if (array2)
      for(var item in array2) {
	ra[item] = array2[item];
      }
    return ra;
  }
  
  /// The rest is just for testing
  this.test = function(src) {
    var boats = db['boatcategories']["Inrigger 2+"];
    boats[1].trip=4242;
  }
  this.valid = function() {
    return valid;
  }

});
