'use strict';
angular.module('eventApp.database.login-services', []).service('LoginService', function($http, $q,$log) {

  var cuser={'user':'lnone'};

  this.getpw = function(data) {
    $http.post('/public/getpw.php', data).then(function(r) {
      alert("password er sendt");
    },function(r) {
      alert("Det mislykkedes at sende nyt password");
    });
  }

  this.setpw = function(data) {
    $http.post('/backend/event/setpw.php', data).then(function(r) {
      alert("password skiftet");
    },function(r) {
      alert("det mislykkedes at skifte password");
    });
  }

  this.get_user = function() {
    return cuser.user;
  }

    this.get_cuser = function() {
    return cuser;
  }

  this.set_user = function(u) {
    cuser.user=u;
  }

  this.check_user = function() {
    var userQ=$q.defer();
    $http.get('/backend/event/current_user.php').then(function(r) {
      cuser.user=r.data;
      userQ.resolve(r.data);
    },function(r) {
      alert("det mislykkedes at se bruger login");
      return {};
    });
    return userQ;
  }

  this.publiccurrentuser = function() {
    return ($http.get('/public/current_user.php'))
  }


  this.logout = function() {
      $http.get('/frontend/README',{'headers':{'AUTHORIZATION':'dummy'}}).then(function(r) {
      cuser={"user":"logget ud"};
      alert("logget ud");
    },function(r) {
      if (r.status==401) {
        cuser.user={"user":"logget Ud"};
      } else {
        alert("log ud problem");
      }
    });

    $http.get('/backend/event/logout.php').then(function(r) {
      cuser={"user":"logget ud"};
      alert("logget ud");
    },function(r) {
      if (r.status==401) {
        cuser.user={"user":"logget UD"};
      } else {
        alert("logget ud problem");
      }
    });
    
  }
  
});
