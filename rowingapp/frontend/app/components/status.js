'use strict';
angular.module('rowApp.status-service', []).service('StatusService', function($http, $q,$log) {
  this.publiccurrentuser = function() {
    return $http.get('/public/current_user.php');
  }  
}).value('version', '0.1');
