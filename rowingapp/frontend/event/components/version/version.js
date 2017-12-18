'use strict';

angular.module('eventApp.version', [
  'eventApp.version.interpolate-filter',
  'eventApp.version.version-directive'
])

.value('version', '0.6');
