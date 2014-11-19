'use strict';

angular.module('myApp.utilities', []).filter('urldecode', function() {
  return function(text) {
    return window.decodeURIComponent(text);
  };
});

angular.module('myApp.utilities', []).filter('urlencode', function() {
  return function(text) {
    return window.encodeURIComponent(text);
  };
});
