angular.module('rowApp.utilities.leveltodk', []).filter('leveltodk', function () {
  return function (lvl) {
    if (lvl>2) return "loft";
    return (lvl?"hylde "+lvl:"gulv");
  };
});
