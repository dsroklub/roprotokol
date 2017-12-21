angular.module('rowApp.utilities.leveltodk', []).filter('leveltodk', function () {
  return function (lvl) {
    return (lvl?"hylde "+lvl:"gulv");
  };
});
