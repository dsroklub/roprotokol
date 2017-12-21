'use strict';

angular.module('rowApp.version', [
  'rowApp.version.interpolate-filter',
  'rowApp.version.version-directive'
])

.value('version', '0.3');
