'use strict';

angular.module('eventApp.database', [
  'eventApp.database.basic-services',
  'eventApp.database.database-services'
])
.value('version', '0.1');
