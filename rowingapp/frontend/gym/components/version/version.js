'use strict';

angular.module('gym.version', [
  'gym.version.interpolate-filter',
  'gym.version.version-directive'
])

.value('version', '0.2');
