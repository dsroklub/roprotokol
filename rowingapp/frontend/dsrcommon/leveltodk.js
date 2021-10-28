angular.module('row.leveltodk', []).filter('leveltodk', function () {
  return function (lvl) {
    if (lvl==3) return "loft";
    if (lvl==4) return "elevator";
    return (lvl?"hylde "+lvl:"gulv");
  };
});
