'use strict';
angular.module('coxApp.database.database-services', []).service('DatabaseService', function($http, $q,$log) {
  var valid={};
  var db={};
  var rowerstatistics={};
  var boatstatistics={};
  var databasesource=dbmode;
  var tx=null;
  var debug=3;
  
  
  var cachedepend={
    'member':['rowers'],
  };
  
  var datastatus={};

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
    $log.debug("DB fetch "+Date());
    var headers = {};
    var promises=[];
    

    this.getData('cox/memberrighttypes',promises);

    if(!valid['rowers']) {
      var rq=$q.defer();
      promises.push(rq.promise);
      $http.get(toURL('cox/rowers.php')).then(function(response) {
        db['rowers'] = [];
        angular.forEach(response.data, function(rower, index) {
          rower.search = (rower.id + " " + rower.name).toLocaleLowerCase();
          this.push(rower);
        }, db['rowers']);
	valid['rowers']=true;
        rq.resolve(true);
      });
    }

//    this.getData('aspirants/cox/current_user',promises);
    
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
    datastatus={
      'boat':null,
      'trip':null,
      'member':null,
      'destination':null
    };
    
    return this.sync(subscriptions);
  }

  this.sync=function(subscriptions) {
    var dbservice=this;
    if (!subscriptions) {
      subscriptions={};
    }
    var sq=$q.defer();
    $http.post('../../backend/cox/datastatus.php', null).then (function(response) {
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
    for (var ti=0; ti<tps.length; ti++) {
      this.invalidate_dependencies(tps[ti]);
    }
    this.sync();
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
    
  this.updateDB_async = function(op,data,config) {
    var qup=$q.defer();
    var res=undefined;
    $http.post('../../backend/'+op+".php", data,config).then(function(r) {
      qup.resolve(r.data)
    },function(r) {
      $log.error(r.status);
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


  this.cox_signup = function(data) {
    var su=$q.defer();
    var res=undefined;
    $http.post('../../backend/cox/aspirants/signup.php', data).then(function(r) {
      su.resolve(r.data);
    },function(r) {
      su.resolve(false);
    });
    datastatus['cox']=null;
    return su;
  }

  this.cox_request = function(data) {
    var su=$q.defer();
    var res=undefined;
    $http.post('../../backend/cox/aspirants/request_team.php', data).then(function(r) {
      su.resolve(r.data);
    },function(r) {
      su.resolve(false);
      $log.debug("COX REQUEST err "+Date());
    });
    datastatus['cox']=null;
    return su;
  }

  
  this.add_cox_requirement = function(data) {
    var adt=$q.defer();
    var res=undefined;
    $http.post('../../backend/cox/add_requirement.php', data).then(function(r) {
      adt.resolve(r.data);
    },function(r) {
      adt.resolve(false);
    });
    datastatus['cox']=null;
    return adt;
  }


  this.add_cox_pass = function(data) {
    var adt=$q.defer();
    var res=undefined;
    $http.post('../../backend/cox/add_pass.php', data).then(function(r) {
      adt.resolve(r.data);
    },function(r) {
      adt.resolve(false);
    });
    datastatus['cox']=null;
    return adt;
  }
  
  this.set_cox_team = function(data) {
    var adt=$q.defer();
    $http.post('../../backend/cox/set_team.php', data).then(function(r) {
      adt.resolve(r.data);
    },function(r) {
      adt.resolve(false);
    });
    datastatus['cox']=null;
    return adt;
  }

  this.add_cox_team = function(data) {
    var adt=$q.defer();
    var res=undefined;
    $http.post('../../backend/cox/add_team.php', data).then(function(r) {
      adt.resolve(r.data);
    },function(r) {
      adt.resolve(false);
    });
    datastatus['cox']=null;
    return adt;
  }
  
  this.deleteCoxTeam = function(data) {
    var dt=$q.defer();
    var res=undefined;
    $http.post('../../backend/cox/deleteteam.php', data).then(function(r) {
      dt.resolve(r.data);
    },function(r) {
      dt.resolve(false);
    });
    datastatus['cox']=null;
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
  this.test = function(src) {
    var boats = db['boatcategories']["Inrigger 2+"];
    boats[1].trip=4242;
  }
  this.valid = function() {
    return valid;
  }

});
