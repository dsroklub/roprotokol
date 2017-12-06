'use strict';

angular.module('gym.database', [
  'gymApp.database.login-services',
  'gym.database.database-services',
  'gym.database.database-directives'
])
.value('version', '0.2');
