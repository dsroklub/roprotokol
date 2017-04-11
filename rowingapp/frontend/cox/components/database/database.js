'use strict';

angular.module('coxApp.database', [
  'coxApp.database.basic-services',
  'coxApp.database.database-services'
])
.value('version', '0.1');
