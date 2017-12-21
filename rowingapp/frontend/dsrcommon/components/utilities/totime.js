angular.module('dsrcommon.utilities.totime', []).filter('totime', function () {
  return function(hours) {
    var hrs = Math.floor(hours);
    var min = Math.round(hours % 1 * 60);
    min = min < 10 ? "0"+min : min.toString();
    return hrs + ":" + min;
  };
});
