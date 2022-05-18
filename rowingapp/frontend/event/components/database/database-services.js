'use strict';

function dbservice($http, $q, $log, $timeout) {
  var valid={};
  var db={'boatsA':[]};
    var db={'boats':[],'boatsById':{},'boatsByName':{},'event/get_row_events':[]};
  var tx=null;
  var debug=3;

  var cachedepend;
  var datastatus={};
  function toURL(service){
      return '/backend/'+service;
  }

  this.onDBerror = function (err) {
    if (err.data && err.data.status && err.data.status=="error") {
      alert(err.data.error);
    } else if (err.statusText) {
      alert("DB error: "+err.statusText);
    } else alert(err);

    if (err.status) {
      $log.debug(" db service err: "+err.status +" " +err.statusText);
    }
  };

  this.getDB = function (dataid) {
    if (db[dataid]===undefined) {
      console.log("missing "+dataid);
    }
    return db[dataid];
  }
  var right2dk={"nothing":"here"};
  var right2dkm={};
  this.getRight2dk = function (r) {
    if (right2dk[r]) {
      return right2dk[r];
    } else {
      return r;
    }
  }
  this.getRight2dkm = function (r) {
    return right2dkm[r];
  }

  this.getData = function (dataid,promises) {
    if(valid[dataid]===false || !db[dataid]) {
      var dq=$q.defer();
      promises.push(dq.promise);
      $http.get(toURL(dataid+'.php'),{}).then(function onSuccess (response) {
        db[dataid] = response.data;
        valid[dataid]=true;
        // $log.debug(" resolve "+dataid);
        dq.resolve(dataid);
      },
       function  (e) {
         $log.debug("getData Fail "+e);
       }
                                             )
    }
  }
  this.getTriptypeWithID = function(tid) {
    for (var i=0; i<db['event/triptypes'].length;i++) {
      if (db['event/triptypes'][i].id==tid) {
        return db['event/triptypes'][i];
      }
    }
  }

  this.simpleGet = function (service, args) {
    var conf = {};
    if (args) {
      conf['params'] = args;
    }
    return $http.get(toURL(service+'.php'),conf);
  }

  this.fetch = function (subscriptions) {
    $log.debug("DB fetch "+Date());
    var headers = {};
    var promises=[];
    if (subscriptions.boat) {
      if(!valid['boats']) {
        //Build indexes and lists for use by API
        // $log.debug("  boats not valid");
        var bq=$q.defer();
        promises.push(bq.promise);
        $http.get(toURL('event/boat_status.php'), { headers: headers } ).then(function (response) {
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
        $http.get(toURL('event/boatdamages.php')).then(function onSuccess(response) {
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
      $http.get(toURL('event/reservations/get_reservations.php')).then(function onSuccess (response) {
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
    if(!valid['memberrighttypes']) {
      var mrq=$q.defer();
      promises.push(mrq.promise);
      $http.get(toURL('event/memberrighttypes.php')).then(function(response) {
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

    this.getData('event/roles',promises);
    this.getData('event/memberrighttypes',promises);
    this.getData('event/forum_files_list',promises);
    this.getData('event/event_category',promises);
    // this.getData('event/vinter_persons',promises); // FIXME: We cannot do caching for this one. Must refresh browser. Or use time limit.
    this.getData('event/messages',promises);
    this.getData('event/member_setting',promises);
    this.getData('event/worklog',promises);
    this.getData('event/triptypes',promises);
    this.getData('event/zones',promises);
    this.getData('event/boats',promises);
    this.getData('event/workers',promises);
    this.getData('event/work_today',promises);
    this.getData('event/rowers',promises);
    this.getData('event/get_row_events',promises);
    this.getData('event/worktasks',promises);
    this.getData('event/boat_category',promises);
    this.getData('event/damage_types',promises);
    this.getData('event/maintenance_boats',promises);
    this.getData('event/current_user',promises);
    this.getData('event/fora',promises);
    //    this.getData('event/events',promises);
    this.getData('event/events_participants',promises);
    this.getData('event/destinations',promises);
    this.getData('event/rights_subtype',promises);
    this.getData('event/errortrips',promises);
    this.getData('event/triptypes',promises);
    this.getData('event/boat_brand',promises);
    this.getData('event/boat_usages',promises);
    this.getData('event/reservations/reservation_configurations',promises);
//    this.getData('event/get_reservations',promises);
    this.getData('event/boatkayakcategory',promises);
    this.getData('event/locations',promises);
    this.getData('event/status',promises);
    this.getData('event/boattypes',promises);
    this.getData('event/userfora',promises);
    this.getData('event/boatdamages',promises);
    this.getData('event/stats/rostat',promises);
    $log.debug("DB fetch rowers");
    var qll=$q.all(promises);
    tx=qll;
    return qll;
  };


  this.invalidate_dependencies=function(tp) {
    for (var di=0;cachedepend[tp] && di < cachedepend[tp].length;di++) {
      var subtp=cachedepend[tp][di];
      valid[subtp]=false;
    }
  };

  datastatus={
    'gitrevision':null,
    'member':null,
    'message':null,
    'boat':null,
    'event':null,
    'fora':null,
    'destination':null,
    'file':null
  };

  db.boatlevels={
    0:'',
    1:'Let',
    2:'Mellem',
    3:'Svær',
    4:'Meget svær'
  }
  this.init = function(subscriptions) {
    cachedepend={
      'status':['event/status','event/reservations/get_reservations'],
      'admin':['event/memberrighttypes','event/rights_subtype','event/errortrips','event/locations','event/get_row_events','boatlevels','event/triptypes','event/destinations'],
	'reservation':['event/boats','event/reservations/get_reservations','event/triptypes'],
      'member':['event/rowers','event/events_participants'],
      'event':['event/events','event/event_category','event/userfora','event/events_participants'],
      'message':['event/messages'],
      'destination':['event/destinations','event/zones'],
      'boat':['event/damage_types','event/boatdamages','event/boats'],
      'work':['event/work_today','event/workers','event/worklog','event/worktasks','event/maintenance_boats'],
      'fora':['event/messages','event/userfora','event/fora'],
      'file':['event/forum_files_list']
    };
    return this.sync(subscriptions);
  }

  this.noinit = function(subscriptions) {
    $log.debug("DB init now sync "+subscriptions);
    return this.sync(subscriptions);
  }

  this.sync=function(subscriptions) {
    var dbservice=this;
    if (!subscriptions) {
      subscriptions={};
    }
    var sq=$q.defer();
    $http.post('/backend/event/datastatus.php', null).then (function(response) {
      var ds=response.data;
      var doreload=false;
      //      $log.debug("got ds" + JSON.stringify(ds)+ "'\ndatastatus="+JSON.stringify(datastatus) +"\n subs="+ JSON.stringify(subscriptions));
      if (gitrevision != ds.gitrevision) {
        $log.info("new git revision " +gitrevision +" --> "+ ds.gitrevision);
        window.location="/front"+ds.gitrevision+"/event/index.shtml";
      }
      for (var tp in ds) {
    if ((!ds[tp] ||  datastatus[tp]!=ds[tp]) && (!subscriptions || subscriptions[tp])) {
      //$log.debug("  doinvalidate "+tp+ " ds[rp]="+ds[tp]+" datastatus[tp]="+datastatus[tp]);
      dbservice.invalidate_dependencies(tp);
      doreload=true;
      datastatus[tp]=ds[tp];
    }
      }
      if (doreload) {
        $log.debug(" do reload " + JSON.stringify(valid));
        dbservice.fetch(subscriptions).then(function() {
      sq.resolve("sync done");
    });
      } else {
        sq.resolve("nothing to do");
      }
    }, function (e) {
      $log.debug(e);
    });
    return sq.promise;
  }

  this.reload=function (tps) {
    for (var ti=0; ti<tps.length; ti++) {
      this.invalidate_dependencies(tps[ti]);
    }
    this.init();
  }

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


  this.getDataNow = function(dataid,arg,onSuccess) {
    var a="";
    if (arg) {
      a="?"+arg;
    }
    $http.get(toURL(dataid+'.php'+a)).then(onSuccess,this.onDBerror);
  }

  this.getRower = function(val) {
    var rs=db['event/rowers'].filter(function(element) {
      return element['id']==val;
    });
    return rs[0];
  }

  this.getRowersByNameOrId = function(nameorid, preselected) {
    var val = nameorid.trim().toLowerCase();
    if (!preselected) {
      preselected=[];
    }
    var ps=[];
    for (var pi=0; pi<preselected.length; pi++) {
      ps[preselected[pi].member_id]=1;
    }
    if (val.length<3 && isNaN(val)) {
      return [];
    }
    var rowers=db['event/rowers'];
    if (!rowers) {
      return [];
    }
    if (isNaN(val)) {
      var re=new RegExp("(\\s|^)"+val,'i');
      var result = rowers.filter(function(element) {
        return (!(element.id in ps) && re.test(element['name']));
      });
      return result;
    } else {
      var result = rowers.filter(function(element) {
        return (!(element.id in ps) && element.id==val);
        });
      return result;
    }
  };

  this.updateDB_async = function(op,data,config) {
    var qup=$q.defer();
    var res=undefined;
    $http.post('/backend/'+op+".php", data,config).then(function(r) {
      qup.resolve(r.data)
    },function(r) {
      $log.error("updataDB",r.status + ": "+op);
      qup.reject(r);
    });
    datastatus['message']=null;
    datastatus['event']=null;
    datastatus['boat']=null;
    datastatus['member']=null;
    datastatus['fora']=null;
    datastatus['notes']=null;
    datastatus['file']=null;
    datastatus['destination']=null;
    return qup.promise;
  }

  this.updateDB = function(op,data,config,eh) {
    $log.debug(' do '+op);
    var ar=this.updateDB_async(op,data,config);
     var at=ar.then(function (res) {
       // $log.debug(' done '+op+" res="+JSON.stringify(res)+" stat "+res.status);
       if (!res||res.status=="notauthorized") {
         $log.error("updatedb error "+op+JSON.stringify(data));
         if (eh) {
           eh(res);
         }
       }
       return res;
     },this.onDBerror
                   );
    return at;
  }

  this.createSubmit = function(entity,data) {
    var entityCreated=$q.defer();
    var res=undefined;
    $http.post('/backend/event/'+entity+'.php', data).then(function(r) {
      entityCreated.resolve(r.data);
    },function(r) {
      var err=entity+"  fejl";
      if (r.data.error) {
        err=r.data.error;
      }
      entityCreated.resolve({"error":err});
    });
    datastatus['event']=null;
    return entityCreated;
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
    $http.post('/public/getpw.php', data).then(function(r) {
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



}

angular.module('eventApp.database.database-services', []).service('DatabaseService', ['$http','$q','$log','$timeout',dbservice]);
