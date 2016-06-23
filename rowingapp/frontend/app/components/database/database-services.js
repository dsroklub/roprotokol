'use strict';
angular.module('myApp.database.database-services', []).service('DatabaseService', function($http, $q,$log) {
  var valid={};
  var db={};
  var rowerstatistics={};
  var boatstatistics={};
  var databasesource=dbmode;
  var tx=null;
  var debug=3;
  
  db.boatlevels={
    1:'Let',
    2:'Mellem',
    3:'Svær',
    4:'Meget svær'
  }
  
  // FIXME Not used?
  this.boatcat2dk = {
      'any':'alle',
      'rowboat':'robåd',
      'kayak':'kajak',
      'kaniner':'kaniner'
  };


  var cachedepend={
    'reservation':['reservation','boat'],
    'boat':['boats','boatdamages','availableboats','reservations','boat_status','boat_usages','boat_status','get_events'],
    'trip':['rowers','rowerstatisticsany','rowerstatisticskayak','rowerstatisticsrowboat', 'boats','errortrips','get_events','errortrips','boat_statistics','membertrips','onwater','rowertripsaggregated','tripmembers','tripstoday','triptypes'],
    'member':['boats','rowers','rower_statisticsany','rowerstatisticsanykayak','rowerstatisticsanyrowboat'],
    'destination':['destinations']
  };
  
  var datastatus={
    'boat':null,
    'trip':null,
    'member':null,
    'destination':null
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
    if(!valid[dataid] || !db[dataid]) {
      var dq=$q.defer();
      promises.push(dq.promise);
      $http.get(toURL(dataid+'.php')).then(function(response) {
        db[dataid] = response.data;
	valid[dataid]=true;
        dq.resolve(dataid);
      });
    }
  }

  this.fetch = function () {
    var boatmaintypes = ['kayak','any','rowboat'];
    $log.debug("DB fetch "+Date());
    var headers = {};
    var promises=[];

    if(!valid['boats']) {
      //Build indexes and lists for use by API
      $log.debug("  boats not valid");
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

    this.getData('destinations',promises);
    this.getData('get_reservations',promises);
    this.getData('get_events',promises);
    this.getData('boattypes',promises);
    this.getData('errortrips',promises);
    this.getData('triptypes',promises);
    this.getData('locations',promises);
    this.getData('boatkayakcategory',promises);
    this.getData('memberrighttypes',promises);
    this.getData('boat_brand',promises);
    this.getData('boat_usages',promises);    
    this.getData('rights_subtype',promises);    

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
    
    var currentyear=true;
    var thisYear=new Date().getFullYear();
    var firstYear=2010;
    
    for (var y=thisYear; y>=firstYear; y--) {
      if (!rowerstatistics[y]) {
        rowerstatistics[y]={'rowboat':[],'kayak':[],'any':[]};
      }
      if (!boatstatistics[y]) {
        boatstatistics[y]={'rowboat':[],'kayak':[],'any':[]};
      }
      for (var bi=0; bi<boatmaintypes.length; bi++) {
        var boattype= boatmaintypes[bi];        

        if ( (y==thisYear && !valid['rowerstatistics'+boattype]) || !rowerstatistics[y][boattype]  || rowerstatistics[y][boattype].length<1) {
          rowerstatistics[y][boattype]=[];
	  (function (bt) {
            var year=y;
	    var sq=$q.defer();
	    promises.push(sq.promise);
            var farg="?season="+year;
	    if (bt != "any") {
	      farg+='&boattype='+bt;
	    }      
	    $http.get(toURL('rower_statistics.php'+farg)).then(function(response) {
              angular.forEach(response.data, function(stat, index) {
                //stat.search = stat.id + " " + stat.firstname + " " + stat.lastname;
                this.push(stat);
              }, rowerstatistics[year][bt]);
	      valid['rowerstatistics'+boattype]=true;	  
	      sq.resolve(true);
	    });
	  })(boattype);
        }
        
        if((y==thisYear && !valid['boatstatistics'+boattype])  || !boatstatistics[y][boattype] ||  boatstatistics[y][boattype].length<1) {
          boatstatistics[y][boattype]=[];
	  (function (bt) {
            var year=y;
	    var sq=$q.defer();
	    promises.push(sq.promise);
            var farg="?season="+year;
	    if (bt != "any") {
	      farg+='&boattype='+bt;
	  }      
	    $http.get(toURL('boat_statistics.php'+farg)).then(function(response) {
              angular.forEach(response.data, function(stat, index) {
                this.push(stat);
              }, boatstatistics[year][bt]);
	      valid['boatstatistics'+boattype]=true;	  
	      sq.resolve(true);
	    });
	  })(boattype);
        }
      }
      currentyear=false;
    }    
    var qll=$q.all(promises);
    tx=qll;
    return qll;
  };

  
  this.defaultLocation = 'DSR';
  this.invalidate_dependencies=function(tp) {
//    $log.debug("  dirty: "+tp);
    for (var di=0;cachedepend[tp] && di < cachedepend[tp].length;di++) {
      var subtp=cachedepend[tp][di];
//      $log.debug("    invalidate: "+subtp);
      valid[subtp]=false;	    
    }
  };

  this.init = function() {
    return this.sync();
  }

  this.sync=function() {
    var dbservice=this;
    var sq=$q.defer();
    $http.post('../../backend/datastatus.php', null).success(function(ds, status, headers, config) {
      var doreload=false;
      $log.debug("got dbstatus" + JSON.stringify(ds));
      for (var tp in ds) {
	if (datastatus[tp]!=ds[tp]) {
//          $log.debug("  inval "+tp);
	  dbservice.invalidate_dependencies(tp);
	  doreload=true;
	}
	datastatus[tp]=ds[tp];
      }
      if (doreload) {
	$log.debug(" do reload " + JSON.stringify(valid));
	dbservice.fetch().then(function() {
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
      this.invalidate_dependencies(tps[ti]);
    }
    this.init();
  }

  this.getBoatTypes = function () {
    return db['boattypes'];
  };

  this.getBoatTypeWithName = function (name) {
    for (var i=0;i<db['boattypes'].length;i++) {
      if (db['boattypes'][i].name==name) {
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
  

  this.lookup = function (resource,key,value) {
    for (var i=0;i<db[resource].length;i++) {
      if (db[resource][i][key]==value) return db[resource][i];
    }
    return null;
  }

  this.nameSearch = function (list,name) {
    for (var i=0;list && (i<list.length);i++) {
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

  this.getBoatTripsAggregated = function (boat,onSuccess) {
    this.getDataNow('boattripsaggregated','boat='+boat.id,onSuccess);
  }
  this.getBoatTrips = function (boat,onSuccess) {
    this.getDataNow('boattrips','boat='+boat.id,onSuccess);
  }
  this.getRowerTripsAggregated = function (member,onSuccess) {
    this.getDataNow('rowertripsaggregated','member='+member.id,onSuccess);
  }
  this.getRowerTrips = function (member,onSuccess) {
    this.getDataNow('rowertrips','member='+member.id,onSuccess);
  }
  this.getDateTrips = function (tripdate,onSuccess) {
    this.getDataNow('datetrips','tripdate='+tripdate,onSuccess);
  }
  this.getTripMembers = function (tripid,onSuccess) {
    this.getDataNow('tripmembers','trip='+tripid,onSuccess);
  }  
  this.getRowerStatistics = function (bt,y) {
    return rowerstatistics[y][bt];
  };
  this.getBoatStatistics = function (bt,y) {
    return boatstatistics[y][bt];
  };

  this.getRower = function(val) {
    var rs=db['rowers'].filter(function(element) {
      return element['id']==val;
    });
    return rs[0];
  }

  this.getRowersByNameOrId = function(nameorid, preselectedids) {
    var val = nameorid.toLowerCase();
    var rowers=db['rowers'];
    if (rowers) {
      var result = rowers.filter(function(element) {
        return (preselectedids === undefined || !(element.id in preselectedids)) && element['search'].indexOf(val) > -1;
      });
      return result;
    } else {
      return [];
    }    
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

  this.updateDB_async = function(op,data,config) {
    var qup=$q.defer();
    var res=undefined;
    $http.post('../../backend/'+op+".php", data,config).success(function(sdata,status,headers,config) {
      qup.resolve(sdata);
    }).error(function(sdata,status,headers,config) {
      $log.error(status);
      qup.resolve(false);
    });
    datastatus['trip']=null;
    datastatus['boat']=null;
    datastatus['member']=null;
    return qup.promise;
  }
  
  this.updateDB = function(op,data,config,eh) {
    $log.debug(' do '+op);
    var ar=this.updateDB_async(op,data,config);
     var at=ar.then(function (res) {
       $log.debug(' done '+op+" res="+JSON.stringify(res)+" stat "+res.status);
       if (!res||res.status=="notauthorized") {
         console.log("auth error "+op+JSON.stringify(data));
         if (eh) {
           eh(res)}
         ;
       }
       return res;
     }                                    
                   );
    return at;
  }

  this.createTrip = function(data) {
    var tripCreated=$q.defer();
    var res=undefined;
    $http.post('../../backend/createtrip.php', data).success(function(sdata,status,headers,config) {
      tripCreated.resolve(sdata);
    }).error(function(sdata,status,headers,config) {
      tripCreated.resolve(false);
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

  this.client_name =function () {
    var clientname="terminal";
    if (localStorage) {
      clientname=localStorage.getItem("roprotokol.client.name");                    
    }
    return(clientname?clientname:"noname");
  }

  this.toIsoDate= function (d) {
      return (d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate());
  };


  
  /// The rest is just for testing
  this.test = function(src) {
    var boats = db['boatcategories']["Inrigger 2+"];
    boats[1].trip=4242;
  }
  this.valid = function() {
    return valid;
  }

});
