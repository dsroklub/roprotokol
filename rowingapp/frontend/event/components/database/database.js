'use strict';

angular.module('eventApp.database', [
  'eventApp.database.login-services',
  'eventApp.database.database-services'
])
.value('version', '0.2');
