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
    'team':['team']
  };
  
  var datastatus={
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
    var qll=$q.all(promises);
    tx=qll;
    return qll;
  };

  
  this.defaultLocation = 'DSR';
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
      $log.debug("got ds" + JSON.stringify(ds)+ "das="+JSON.stringify(datastatus) +"subs="+ JSON.stringify(subscriptions));
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

  this.getRowersByNameOrId = function(nameorid, preselectedids) {
    var val = nameorid.trim().toLowerCase();
    if (val.length<3 && isNaN(val)) {
       return [];
    }
    var rowers=db['rowers'];
    if (!rowers) {
      return [];
    }

    if (isNaN(val)) {
        var result = rowers.filter(function(element) {
          return (preselectedids === undefined || !(element.id in preselectedids)) && element['search'].indexOf(val) > -1;
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
