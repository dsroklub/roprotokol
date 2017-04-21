'use strict';
angular.module('eventApp.database.basic-services', []).service('BasicService', function($http, $q,$log) {

  this.getpw = function(data) {
    $http.post('../../../public/getpw.php', data).then(function(r) {
      alert("password er sendt");
    },function(r) {
      alert("det mislykkedes at sende nyt password");
    });
  }

  this.current_user = function() {
    $http.get('../../../public/current_user.php').then(function(r) {
      return(r.data);
    },function(r) {
      alert("det mislykkedes at se bruger login");
    });
  }


  this.logout = function() {
    $http.get('../../../backend/event/logout.php').then(function(r) {
      alert("logget ud");
    },function(r) {
      alert("logget ud problem");
    });
  }
  
});
