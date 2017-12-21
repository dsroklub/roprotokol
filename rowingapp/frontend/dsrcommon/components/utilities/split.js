angular.module('dsrcommon.utilities.split', []).filter('split', mySplit);

function mySplit() {
  return function( str, separator) {
    if (!str) {
      return [];
    }
    if (!separator) {
      separator = ',';
    }
    return str.split(separator);
  };
}
