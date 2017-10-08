'use strict';

angular.module('coxApp.database', [
  'coxApp.login-services',
  'coxApp.database.database-services'
])
.value('version', '0.1');
