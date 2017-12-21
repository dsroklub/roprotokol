angular.module('dsrcommon.utilities.ifNull', []).filter('ifNull', function () {
  return function( val, defaultVal, suffix) {
    if (val === null) return defaultVal;
    if (suffix != null) {
      val += suffix;
    }
    return val;
  };
});
