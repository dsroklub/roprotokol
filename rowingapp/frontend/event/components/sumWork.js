angular.module('event.utilities.sumWork', []).filter('sumWork',sw);
function sw() {
    return function( arr, start, len) {
      var tot=0;
      arr.forEach(function (w) {
        tot += 1.0*w.hours;
      });
      return tot;
    };
}
