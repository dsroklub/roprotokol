'use strict';
angular.module('eventApp.database.basic-services', []).service('BasicService', function($http, $q,$log) {

this.getpw = function(data) {
    $http.post('../../../public/getpw.php', data).then(function(r) {
      alert("password er sendt");
    },function(r) {
      alert("det mislykkedes at sende nyt password");
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
