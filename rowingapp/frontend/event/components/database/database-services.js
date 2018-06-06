'use strict';

function dbservice($http, $q, $log) {
  var valid={};
  var db={};
  var tx=null;
  var debug=3;
    
  var cachedepend;
  var datastatus={};

  
  function toURL(service){
      return '/backend/'+service;
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
      //      $http.get(toURL(dataid+'.php'),{headers:{"X-Force-Content-Type":"application/json; charset=UTF-8"}}).then(function onSuccess (response) {
      $http.get(toURL(dataid+'.php'),{}).then(function onSuccess (response) {
        db[dataid] = response.data;
	valid[dataid]=true;
        dq.resolve(dataid);
      });
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
    this.getData('event/roles',promises);
    this.getData('event/memberrighttypes',promises);
    this.getData('event/forum_files_list',promises);
    this.getData('event/event_category',promises);
    this.getData('event/messages',promises);
    this.getData('event/member_setting',promises);
    this.getData('event/boat_category',promises);
    this.getData('event/current_user',promises);
    this.getData('event/fora',promises);
    //    this.getData('event/events',promises);
    this.getData('event/events_participants',promises);
    this.getData('event/destinations',promises);
    this.getData('event/userfora',promises);
    if(!valid['event/rowers']) {
      var rq=$q.defer();
      promises.push(rq.promise);
      $http.get(toURL('event/rowers.php')).then(function(response) {
        db['event/rowers'] = [];
        angular.forEach(response.data, function(rower, index) {
          rower.search = (rower.id + " " + rower.name).toLocaleLowerCase();
          this.push(rower);
        }, db['event/rowers']);
	valid['event/rowers']=true;
        rq.resolve(true);
      });
    }

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
    'event':null,
    'fora':null,
    'destination':null,
    'file':null
  };

  this.init = function(subscriptions) {
    cachedepend={
      'member':['event/rowers','event/events_participants'],
      'event':['event/events','event/event_category','event/userfora','event/events_participants'],
      'message':['event/messages'],
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
        window.location="/frontend/event/index.shtml";
        window.location.reload(true);
      }
      for (var tp in ds) {
	if ((!ds[tp] ||  datastatus[tp]!=ds[tp]) && (!subscriptions || subscriptions[tp])) {
          $log.debug("  doinvalidate "+tp);
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
    $http.get(toURL(dataid+'.php'+a)).then(onSuccess);
  }
  
  this.getRower = function(val) {
    var rs=db['event/rowers'].filter(function(element) {
      return element['id']==val;
    });
    return rs[0];
  }

  this.getRowersByNameOrId = function(nameorid, preselectedids) {
    var val = nameorid.toLowerCase();
    var rowers=db['event/rowers'];
    if (rowers) {
      var result = rowers.filter(function(element) {
        return (preselectedids === undefined || !(element.id in preselectedids)) && element['search'].indexOf(val) > -1;
      });
      return result;
    } else {
      return [];
    }    
  };
    
  this.updateDB_async = function(op,data,config) {
    var qup=$q.defer();
    var res=undefined;
    $http.post('/backend/'+op+".php", data,config).then(function(r) {
      qup.resolve(r.data)
    },function(r) {
      $log.error(r.status);
      qup.resolve(false);
    });
    datastatus['message']=null;
    datastatus['event']=null;
    datastatus['boat']=null;
    datastatus['member']=null;
    return qup.promise;
  }
  
  this.updateDB = function(op,data,config,eh) {
    $log.debug(' do '+op);
    var ar=this.updateDB_async(op,data,config);
     var at=ar.then(function (res) {
       // $log.debug(' done '+op+" res="+JSON.stringify(res)+" stat "+res.status);
       if (!res||res.status=="notauthorized") {
         $log.error("auth error "+op+JSON.stringify(data));
         if (eh) {
           eh(res);
         }         
       }
       return res;
     }                                    
                   );
    return at;
  }

  this.createSubmit = function(entity,data) {
    var entityCreated=$q.defer();
    var res=undefined;
    $http.post('/backend/event/'+entity+'.php', data).then(function(r) {
      entityCreated.resolve(r.data);
    },function(r) {
      entityCreated.resolve({"error":entity+"  fejl"});
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
}

angular.module('eventApp.database.database-services', []).service('DatabaseService', ['$http','$q','$log',dbservice]);
