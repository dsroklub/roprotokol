'use strict';
angular.module('gym.database.database-services', []).service('DatabaseService', function($http, $q,$log) {
  var valid={};
  var db={};
  var rowerstatistics={};
  var databasesource=dbmode;
  var tx=null;
  var debug=3;
  var cachedepend={
    'member':['rowers'],
    'gym':['team/team','team/attendance','team/teamStats','team/teamNames']
  };
  
  var datastatus={
    'member':null,
    'gym':null,
    'stats':null,
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
  //  $log.debug(" getData: " + dataid);
    if(!valid[dataid] || !db[dataid]) {
      var dq=$q.defer();
      promises.push(dq.promise);
      $http.get(toURL(dataid+'.php')).then(function onSuccess (response) {
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
    return $http.get(toURL(service+'.php',conf));
  }

  this.fetch = function (subscriptions) {
    var headers = {};
    var promises=[];
    this.getData('team/team',promises);
    this.getData('team/attendance',promises);
    this.getData('team/teamNames',promises);
    this.getData('team/teamStats',promises);
    if(!valid['rowers']) {
      var rq=$q.defer();
      promises.push(rq.promise);
      $http.get(toURL('team/rowers.php')).then(function(response) {
        db['rowers'] = response.data;
	valid['rowers']=true;
        rq.resolve(true);
      });
    }
    var currentyear=true;
    var thisYear=new Date().getFullYear();
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
  this.init = function(subscriptions) {
    return this.sync(subscriptions);
  }

  this.sync = function(subscriptions) {
    var dbservice=this;
    if (!subscriptions) {
      subscriptions={};
    }
    var sq=$q.defer();
    $http.post('../../backend/datastatus.php', null).then (function(response) {
      var ds=response.data;
      var doreload=false;
      // $log.debug("got ds" + JSON.stringify(ds)+ "das="+JSON.stringify(datastatus) +"subs="+ JSON.stringify(subscriptions));
      for (var tp in ds) {
	if ((!ds[tp] ||  datastatus[tp]!=ds[tp]) && (!subscriptions || subscriptions[tp])) {
          $log.debug("  inval "+tp); // NEL
	  dbservice.invalidate_dependencies(tp);
	  doreload=true;
	}
	datastatus[tp]=ds[tp];
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
    var subs={};
    for (var ti=0; ti<tps.length; ti++) {
      var tag=tps[ti];
      this.invalidate_dependencies(tag);
      subs[tag]=true;
    }
    this.sync(subs);
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

  this.getDestinations = function (location) {
    var loc='DSR';
    if(location !== undefined) {
      loc=location;
    }
      return db['destinations'][loc];
  };

  this.getDataNow = function(dataid,arg,onSuccess) {
    var a="";
    if (arg) {
      a="?"+arg;
    }
    $http.get(toURL(dataid+'.php'+a)).then(onSuccess);
  }

  this.getTeams = function (onSuccess) {
    this.getDataNow('team/team',null,onSuccess);
  }
  this.getTripMembers = function (tripid,onSuccess) {
    this.getDataNow('tripmembers','trip='+tripid,onSuccess);
  }

  this.getRower = function(val) {
    var rs=db['rowers'].filter(function(element) {
      return element['id']==val;
    });
    return rs[0];
  }

  this.getRowersByNameOrId = function(nameorid, attendance,team) {
    var val = nameorid.trim().toLowerCase();
    if (val.length<3 && isNaN(val)) {
       return [];
    }
    var rowers=db['rowers'];
    if (!rowers) {
      return [];
    }

    var result;
    if (isNaN(val)) {
	var re=new RegExp("(\\s|^)"+val,'i');
	result = rowers.filter(function(element) {
	    return (re.test(element['name']));
	});
    } else {
	result = rowers.filter(function(element) {
        return (element.id==parseInt(val));
      });
    }
    return result.filter(function(rower) {
      for (var i=0;i<attendance.length;i++) {
        if (attendance[i].memberid==rower.id && attendance[i].timeofday==team.timeofday && attendance[i].team==team.name) {
          return false;
        }
      }
      return true;
    })
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
      $log.error(r.status);
      qup.resolve(false);
    });
    datastatus['member']=null;
    return qup.promise;
  }

  this.updateDB = function(op,data,config,eh) {
    $log.debug(' do '+op);
    var ar=this.updateDB_async(op,data,config);
     var at=ar.then(function (res) {
       $log.debug(' done '+op+" res="+JSON.stringify(res)+" stat "+res.status);
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

  this.attendTeam = function(data) {
    var attendance=$q.defer();
    var res=undefined;
    $http.post('../../backend/team/register.php', data).then(function(r) {
      attendance.resolve(r.data);
    },function(sdata,status,headers,config) {
      attendance.resolve(false);
    });
    datastatus['gym']=null;
    return attendance;
  }


  this.addTeam = function(data) {
    var adt=$q.defer();
    var res=undefined;
    $http.post('../../backend/team/addteam.php', data).then(function(r) {
      adt.resolve(r.data);
    },function(r) {
      adt.resolve(false);
    });
    datastatus['gym']=null;
    return adt;
  }

  this.deleteTeam = function(data) {
    var dt=$q.defer();
    var res=undefined;
    $http.post('../../backend/team/deleteteam.php', data).then(function(r) {
      dt.resolve(r.data);
    },function(r) {
      dt.resolve(false);
    });
    datastatus['gym']=null;
    return dt;
  }

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
  this.valid = function() {
    return valid;
  }

});
