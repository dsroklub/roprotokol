'use strict';

angular.module('eventApp.version.version-directive', [])

.directive('eventVersion', ['version', function(version) {
  return function(scope, elm, attrs) {
    elm.text(version);
  };
}]);
