angular.module('rowApp.utilities.mtokmint', []).filter('mtokmint', function () {
  return function (m) {
    return (m / 1000).toFixed(0);
  };
});
