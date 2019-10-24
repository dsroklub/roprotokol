angular.module('event.utilities.sumWork', []).filter('sumWork',sw);
function sw() {
  return function( arr, start, len) {
    var tot=0;
    if (arr) {
      arr.forEach(function (w) {
        tot += 1.0*w.hours;
      });
    }
    return tot;
  };
}

angular.module('event.utilities.sumDistance', []).filter('sumDistance',sd);
function sd() {
  return function( arr, start, len) {
    var tot=0;
    if (arr) {
      arr.forEach(function (w) {
        tot += 1.0*w.distance;
      });
    }
    return tot/1000;
  };
}
