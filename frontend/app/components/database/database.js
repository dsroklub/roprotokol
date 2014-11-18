'use strict';

angular.module('myApp.database', [
  'myApp.database.database-services',
  'myApp.database.database-directives'
])
.value('version', '0.1');
