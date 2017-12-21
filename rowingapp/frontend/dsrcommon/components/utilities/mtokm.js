angular.module('dsrcommon.utilities.mtokm', []).filter('mtokm', function () {
  return function (meters) {
    return (meters / 1000).toFixed(1);
  };
});
