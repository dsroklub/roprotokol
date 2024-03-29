'use strict';
function dbservice($http, $q, $log) {
  var valid={};
  var db={'boatsA':[]};
  var rowerstatistics=[];
  var boatstatistics=[];
  var databasesource=dbmode;
  var reservation_configurations=[];
  var cachedepend;
  var currentseason=new Date().getFullYear();

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

  var right2dk={"nothing":"here"};
  var right2dkm={};
  this.getRight2dk = function (r) {
    return right2dk[r];
  }
  this.getRight2dkm = function (r) {
    return right2dkm[r];
  }

  this.getData = function (dataid,arg,promises) {
    var a="";
    if (arg) {
      a="?"+arg;
    }
  //  $log.debug(" getData: " + dataid);
    if(!valid[dataid] || !db[dataid+""+arg]) {
      var dq=$q.defer();
      promises.push(dq.promise);
      $http.get(toURL(dataid+'.php'+a)).then(function onSuccess (response) {
        db[dataid+""+arg] = response.data;
        valid[dataid]=true;
        dq.resolve(dataid);
      });
      return true;
    }
    return false;
  }


  this.simpleGet = function (service, args) {
    var conf = {};
    if (args) {
      conf['params'] = args;
    }
    return $http.get(toURL(service+'.php',conf));
  }

  this.fetch = function (subscriptions) {
    var boatmaintypes = ['kayak','any','rowboat'];
    //$log.debug("DB fetch "+Date());
    var headers = {};
    var promises=[];
    if (subscriptions.boat) {
      if(!valid['boats']) {
        //Build indexes and lists for use by API
        // $log.debug("  boats not valid");
        var bq=$q.defer();
        promises.push(bq.promise);
        $http.get(toURL('boat_status.php'), { headers: headers } ).then(function (response) {
          db['boats'] = {};
          var boatsA=[];
          angular.forEach(response.data, function(boat, index) {
            db['boats'][boat.id] = boat;
            boatsA.push(boat);
          }, boatsA);
          db['boatsA']=boatsA;
          var boatCategories={};
          angular.forEach(response.data, function(boat, index) {
            var category = boat.category;
            if(this[category] === undefined) {
              this[category] = [];
            }
            this[category].push(boat);
          }, boatCategories);
          db['boatcategories'] = boatCategories;
          valid['boats']=true;
          bq.resolve(true);
        });
      }

      if (!valid['boatdamages']) {
        var boatdamageq=$q.defer();
        promises.push(boatdamageq.promise);
        $http.get(toURL('boatdamages.php')).then(function onSuccess(response) {
          var boatdamages={};
          db['boatdamages_flat'] = response.data;
          angular.forEach(db['boatdamages_flat'], function(boatdamage, index) {
            if(this[boatdamage.boat_id] === undefined) {
              this[boatdamage.boat_id] = [];
            }
            this[boatdamage.boat_id].push(boatdamage);
          }, boatdamages);
          db['boatdamages'] = boatdamages;
          valid['boatdamages']=true;
          boatdamageq.resolve(true);
        });
      }
    }
    if (!valid['get_reservations']) {
      var reservationq=$q.defer();
      promises.push(reservationq.promise);
      $http.get(toURL('get_reservations.php')).then(function onSuccess (response) {
        db["get_reservations"] = response.data;
        var reservationsByBoat=[];
        var reservations=db['get_reservations'];
        for (var ri=0; ri<reservations.length;ri++) {
          if (!reservationsByBoat[reservations[ri].boat_id]) {
            reservationsByBoat[reservations[ri].boat_id]=[];
          }
          //if (reservations[ri].dayofweek<1 || reservations[ri].configuration==status.reservertion_configuration) {
            reservationsByBoat[reservations[ri].boat_id].push(reservations[ri]);
          //}
        }
        db['reservationsByBoat']=reservationsByBoat;
        valid["get_reservations"]=true;
        reservationq.resolve("get_reservations");
      });
    }
    this.getData('destinations',"",promises);
    this.getData('get_reservations',"",promises);
    this.getData('get_events',"",promises);
    this.getData('boattypes',"",promises);
    this.getData('errortrips',"",promises);
    this.getData('triptypes',"",promises);
    this.getData('zones',"",promises);
    this.getData('onwater',"",promises);
    this.getData('guest_stat',"",promises);
    this.getData('locations',"",promises);
    this.getData('reservation_configurations',"",promises);
    this.getData('boatkayakcategory',"",promises);
    this.getData('boat_brand',"",promises);
    this.getData('boat_usages',"",promises);
    this.getData('damage_types',"",promises);
    this.getData('rights_subtype',"",promises);
    this.getData('status',"",promises);
    this.getData('boat_notes',"",promises);
    this.getData('stats/trip_stat_year',"",promises);

    if(!valid['memberrighttypes']) {
      var mrq=$q.defer();
      promises.push(mrq.promise);
      $http.get(toURL('memberrighttypes.php')).then(function(response) {
        var rights=response.data;
        right2dk = {};
        right2dkm = {};
        db['memberrighttypes']= rights;
        for (var mri=0;mri<rights.length;mri++) {
          var r=rights[mri];
          if (r.member_right=="admin") continue;
          right2dk[r.member_right] = r.showname;
          right2dkm[r.member_right] = r.predicate;
        }
        valid['memberrighttypes']=true;
        mrq.resolve(true);
      });
    }

    if(!valid['rowers']) {
      var rowerq=$q.defer();
      promises.push(rowerq.promise);
      $http.get(toURL('rowers.php')).then(function(response) {
        db['rowers']=response.data;
        valid['rowers']=true;
        rowerq.resolve(true);
      });
    }

    var currentyear=true;
    var thisYear=new Date().getFullYear();
    var firstYear=2010;

    if (subscriptions.stats){
      for (var y=thisYear; y>=firstYear; y--) {
        if (!rowerstatistics[y]) {
          rowerstatistics[y]={'rowboat':[],'kayak':[],'any':[]};
        }
        if (!boatstatistics[y]) {
          boatstatistics[y]={'rowboat':[],'kayak':[],'any':[]};
        }
        currentyear=false;
      }
    }
    var qll=$q.all(promises);
    return qll;
  };

  this.invalidate_dependencies=function(tp) {
    for (var di=0;cachedepend[tp] && di < cachedepend[tp].length;di++) {
      var subtp=cachedepend[tp][di];
      // $log.debug(' !v '+subtp);
      valid[subtp]=false;
    }
  };

  var defaultLocation = 'DSR';
  var boatcat2dk;

  var datastatus={
    'boat':null,
    'stats':null,
    'reservation':null,
    'trip':null,
    'member':null,
    'destination':null
  };

  this.init = function(subscriptions) {

    this.boatcat2dk = {
      'any':'alle',
      'rowboat':'robåd',
      'kayak':'kajak',
      'kaniner':'kaniner'
    };

    db.boatlevels={
      0:'',
      1:'Let',
      2:'Mellem',
      3:'Svær',
      4:'Meget svær'
    }
    defaultLocation = 'DSR';
    cachedepend={
      'status':['status','reservation'],
      'admin':['memberrighttypes','rights_subtype','errortrips','locations','get_events'],
      'reservation':['reservation','boats','get_reservations'],
      'boat':['boats','boatdamages','availableboats','boat_status','boat_usages','get_events','onwater','boattypes','destinations','reservation_configurations','memberrighttypes','damage_types'],
      'trip':['rowers', 'boats','errortrips','get_events','errortrips','boat_statistics','membertrips','onwater','rowertripsaggregated','tripmembers','tripstoday','triptypes'],
      'member':['boats','rowers','rowerstatisticsany','rowerstatisticsanykayak','rowerstatisticsanyrowboat','memberrighttypes'],
      'destination':['destinations','zones'],
      'guest':['guest_stat'],
      'stats':['rowerstatisticsany','rowerstatisticskayak','rowerstatisticsrowboat','stats/trip_stat_year'],
      'message':["boat_notes"],
      'archivestats':[]
    };
    return this.sync(subscriptions);
  }

  function reservation_is_current(reservation,reservation_configurations) {
    // if (reservation.dayofweek<1) return true;
    for (var rci=0; rci<reservation_configurations.length; rci++) {
      if (reservation_configurations[rci].selected && reservation_configurations[rci].name==reservation.configuration) return true;
    }
    return false;
  }

  this.update_reservations = function(reservation_configurations,checkout) {
    var chout;
    if (checkout.starttime) {
      chout=checkout.starttime;
    } else {
      chout = new Date();
    }
    var this_dayofweek=chout.getDay();
    if (this_dayofweek==0) {
      this_dayofweek=7;
    }
    var chin;
    if (checkout.expectedtime) {
      chin=checkout.expectedtime;
    } else {
      chin=new Date(chout.getTime()+2*3600*1000);
    }
    var allboats=db['boatsA'];
    var reservationsByBoat=db['reservationsByBoat'];
    for (var bi=0; bi<allboats.length;bi++) {
      allboats[bi].reserved_to=null;
      //console.log("upres "+allboats[bi].name);
      if (reservationsByBoat[allboats[bi].id]) {
        for (var ri=0; ri<reservationsByBoat[allboats[bi].id].length; ri++) {
          var reservation=reservationsByBoat[allboats[bi].id][ri];
          if (reservation_is_current(reservation,reservation_configurations)) {
            var from_time=reservation.start_time.split(":");
            var end_time=reservation.end_time.split(":");
            if (reservation.dayofweek==0) {
              var startdate=new Date(reservation.start_date);
              var enddate=new Date(reservation.end_date);
              enddate.setHours(end_time[0]);
              enddate.setMinutes(end_time[1]);
              startdate.setHours(from_time[0]);
              startdate.setMinutes(from_time[1]);
              if (!(chin<startdate || chout>enddate)) {
                allboats[bi].reserved_triptype=reservationsByBoat[allboats[bi].id][ri].triptype;
                allboats[bi].reserved_to=reservationsByBoat[allboats[bi].id][ri].triptype;
                if (reservationsByBoat[allboats[bi].id][ri].purpose) {
                  allboats[bi].reserved_to+=(" ("+reservationsByBoat[allboats[bi].id][ri].purpose+")");
                }
                break;
              }
            } else if (reservation.dayofweek==this_dayofweek) {
              var startres=new Date;
              var endres=new Date;
              endres.setHours(end_time[0]);
              endres.setMinutes(end_time[1]);
              startres.setHours(from_time[0]);
              startres.setMinutes(from_time[1]);
              if (!(chin<startres || chout>endres)) {
                allboats[bi].reserved_to=reservationsByBoat[allboats[bi].id][ri].triptype;
                allboats[bi].reserved_triptype=reservationsByBoat[allboats[bi].id][ri].triptype;
                if (chout<startres) {
                  allboats[bi].reserved_to+=(" "+from_time[0]+":"+from_time[1]);
                }
                if (reservationsByBoat[allboats[bi].id][ri].purpose) {
                  allboats[bi].reserved_to+=(" ("+reservationsByBoat[allboats[bi].id][ri].purpose+")");
                }
                break;
              }
            }
          }
        }
      }
    }
  }


  this.sync = function(subscriptions) {
    var dbservice=this;
    if (!subscriptions) {
      subscriptions={};
    }
    var sq=$q.defer();
    $http.post('../../backend/datastatus.php', null).then(function(response) {
      var ds=response.data;
      db['current_user']=ds.uid;
      if (gitrevision != ds.gitrevision) {
        $log.info("new git revision " +gitrevision +" --> "+ ds.gitrevision);
        //        window.location="/frontend/app/index.shtml";
        // window.location.pathname="/front"+ds.gitrevision+"/app/";
        // window.location.reload(true);
          // $angularCacheFactory.clearAll();
        //    var cache = $cacheFactory.get('$http');
        //    cache.removeAll();
        // $templateCache.removeAll();
      }

      var doreload=false;
      // $log.log("got ds" + JSON.stringify(ds)+ "das="+JSON.stringify(datastatus) +"subs="+ JSON.stringify(subscriptions));
      for (var tp in ds) {
        if ((ds[tp]==null ||  !(tp in datastatus) || datastatus[tp]!=ds[tp]) && (!subscriptions || subscriptions[tp])) {
          //$log.log("  inval "+tp); // NEL
          dbservice.invalidate_dependencies(tp);
          doreload=true;
          datastatus[tp]=ds[tp];
        }
      }
      if (doreload) {
        //$log.log(" do reload " + JSON.stringify(valid));
        dbservice.fetch(subscriptions).then(function() {
          sq.resolve("sync done");
        });
      } else {
        sq.resolve("nothing to do");
      }
    });
    return sq.promise;
  }

  this.reload=function (tps) {
    var subs={};
    for (var ti=0; ti<tps.length; ti++) {
      var tag=tps[ti];
      this.invalidate_dependencies(tag);
      subs[tag]=true;
    }
    this.sync(subs);
  }

  this.getBoatTypes = function () {
    return db['boattypes'];
  };

  this.getBoatTypeWithName = function (name) {
    for (var bti=0;bti<db['boattypes'].length;bti++) {
      if (db['boattypes'][bti].name==name) {
        return (db['boattypes'][bti]);
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
    if (!db[resource]) {
      return null;
    }
    for (var rsi=0;rsi<db[resource].length;rsi++) {
      if (db[resource][rsi][key]==value) return db[resource][rsi];
    }
    return null;
  }

  this.nameSearch = function (list,name) {
    for (var nsi=0;list && (nsi<list.length);nsi++) {
      if (list[nsi].name==name) return list[nsi];
    }
    return null;
  }

  this.getDestinations = function (location) {
    var loc='DSR';
    if(location !== undefined) {
      loc=location;
    }
    var destinations=db['destinations'];
    if (!destinations) {
      return [];
    }
    return db['destinations'][loc];
  };


  this.getDestinationWithName = function(name,location) {
    for (var dwni=0; dwni<this.getDestinations(location).length;dwni++) {
      var dc=this.getDestinations(location)[dwni];
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
  this.getRowerTripsAggregatedAllTime = function (member,onSuccess) {
    this.getDataNow('rowertripsaggregated','member='+member.id+'&season=all',onSuccess);
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

  this.getBoatStatistics = function (bt,y) {
    var sq=$q.defer();
    if((y==currentseason && !valid['boatstatistics'+bt])  || !boatstatistics[y][bt] || boatstatistics[y][bt].length<1) {
      if (!(y in boatstatistics)) {
        boatstatistics[y]=[];
      }
      boatstatistics[y][bt]=[];
      var farg="?season="+y;
      if (bt != "any") {
        farg+='&boattype='+bt;
      }
      $http.get(toURL('boat_statistics.php'+farg)).then(function(response) {
        angular.forEach(response.data, function(stat, index) {
          this.push(stat);
        }, boatstatistics[y][bt]);
        valid['boatstatistics'+bt]=true;
        sq.resolve(boatstatistics[y][bt]);
      },function(r) {
        $log.error(r.status);
        sq.resolve(false);
      }                                                       );
    } else {
      sq.resolve(boatstatistics[y][bt]);
    }
    return sq.promise;
  };

    this.getRowerStatistics = function (bt,y) {
      var sq=$q.defer();
      if ( (y==currentseason && !valid['rowerstatistics'+bt]) ||
           !rowerstatistics[y][bt]  ||
           rowerstatistics[y][bt].length<1) {
        if (!(y in rowerstatistics)) {
          rowerstatistics[y]=[];
        }
        var farg="?season="+y;
        if (bt != "any") {
          farg+='&boattype='+bt;
        }
        var rsbty=[];
        $http.get(toURL('rower_statistics.php'+farg)).then(function(response) {
          angular.forEach(response.data, function(stat, index) {
            this.push(stat);
          }, rsbty);
          valid['rowerstatistics'+bt]=true;
          rowerstatistics[y][bt]=rsbty;
          sq.resolve(rsbty);
        },function(r) {
          $log.error(r.status);
          sq.resolve(false);
        }                                                       );
    } else {
        sq.resolve(rowerstatistics[y][bt]);
    }
    return sq.promise;
  };

  this.getRower = function(val) {
    var rs=db['rowers'].filter(function(element) {
      return element['id']==val;
    });
    return rs[0];
  }

  this.removeRower = function(rower) {
    var roi = db['rowers'].indexOf(rower);
    if (roi > -1) {
      db['rowers'].splice(roi, 1);
    }
  }

  this.getRowerByMemberId = function(member_id) {
    var rowers=db['rowers'];
    if (!rowers) {
      return null;
    }
    for (var rmi=0; rmi<rowers.length; rmi++) {
      if (rowers[rmi].id==member_id) {
        return rowers[rmi];
      }
    }
  }

  this.getCurrentRower = function() {
    if (!db['current_user']) return null;
    return this.getRowerByMemberId(db['current_user']);
  }

  this.getRowersByNameOrId = function(nameorid, preselectedids) {
    var val = nameorid.trim().toLowerCase();
    if (val.length<3 && isNaN(val)) {
      return [];
    }
    var rowers=db['rowers'];
    if (!rowers) {
      return [];
    }

    if (isNaN(val.substring(1))) {
      var re=new RegExp("(\\s|^)"+val,'i');
      var result = rowers.filter(function(element) {
        return (preselectedids === undefined || !(element.id in preselectedids)) && re.test(element['name']);
      });
      return result;
    } else {
      var result = rowers.filter(function(element) {
          return (preselectedids === undefined || !(element.id in preselectedids)) && element.id==val;
        });
      return result;
    }
  };
  this.closeForm = function(form,data,datakind) {
    var formClosed=$q.defer();
    var res=undefined;
    $http.post('../../backend/'+form+'.php', data).then(function onSuccess (r) {
      formClosed.resolve(r.data);
    }, function onError (r) {
      formClosed.resolve(false);
    });
    datastatus[datakind]=null;
    return formClosed;
  };

  this.updateDB_async = function(op,data,config) {
    var qup=$q.defer();
    var res=undefined;
    $http.post('../../backend/'+op+".php", data,config).then(function(r) {
      qup.resolve(r.data)
    },function(r) {
      qup.resolve(r);
    });
    datastatus['trip']=null;
    datastatus['boat']=null;
    datastatus['member']=null;
    datastatus['status']=null;
    return qup.promise;
  }
  this.updateDB = function(op,data,config,eh) {
    var ar=this.updateDB_async(op,data,config);
     var at=ar.then(function (res) {
       // $log.debug(' done '+op+" res="+JSON.stringify(res)+" stat "+res.status);
       if (!res||(res.status !="ok" && (!res.data||res.data.status=="error"||res.data.status=="notauthorized"))) {
         $log.error("up DB error "+op+JSON.stringify(data));
         if (eh) {
           eh(res);
         }
       }
       return res;
     }
                   );
    return at;
  }

  this.createTrip = function(data) {
    var tripCreated=$q.defer();
    var res=undefined;
    $http.post('../../backend/createtrip.php', data).then(function(r) {
      tripCreated.resolve(r.data);
    },function(r) {
      tripCreated.resolve(false);
    });
    datastatus['trip']=null;
    return tripCreated;
  };

  this.newDamage = function(data) {
    $http.post('../../backend/newdamage.php', data).then(function(r) {
    },function(r) {
      alert("det mislykkedes at tilføje ny skade "+status+" "+data);
    });
    valid['boat']=false;
    return 1;
  };

  this.fixDamage = function(data) {
    $http.post('../../backend/fixdamage.php', data).then(function(r) {
    },function(r) {
      alert("det mislykkedes at klarmelde skade "+status+" "+data);
    });
    valid['boat']=false;
    return 1;
  };


  this.client_name =function () {
    var clientname="terminal";
    if (localStorage) {
      clientname=localStorage.getItem("roprotokol.client.name");
    }
    return(clientname?clientname:"noname");
  }

  this.toIsoDate = function (d) {
      return (d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate());
  };

  this.getpw = function(data) {
    $http.post('../../../public/getpw.php', data).then(function(r) {
    },function(r) {
      alert("det mislykkedes at sende nyt password");
    });
  }

  /// The rest is just for testing
  this.test = function(src) {
    var boats = db['boatcategories']["Inrigger 2+"];
    boats[1].trip=4242;
  }
  this.valid = function() {
    return valid;
  }
}

angular.module('rowApp.database.database-services', []).service('DatabaseService', ['$http','$q','$log',dbservice]);
