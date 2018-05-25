angular.module('rowApp.utilities.subArray', []).filter('subArray', function () {
  return function( arr, start, len) {
    if (! arr.splice ) {
      // console.log("subArray input cannot be spliced", arr);
      return null;
    }
    if (start == null) {
      start = 0;
    }
    return arr.splice(start, len);
  };
});
