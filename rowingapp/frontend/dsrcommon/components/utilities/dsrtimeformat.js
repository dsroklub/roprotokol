  function pad(n) {
    if (n<10) return "0"+n;
    return ""+n;
  }
angular.module('dsrcommon.utilities.dsrtimeformat', []).filter('dsrtimeformat', function () {
  return function(tm) {
    var showdate=false;
    if (showdate) {
      return pad(tm.hour) + ":" + pad(tm.minute) + " "+ pad(tm.day) + "/" + pad(tm.month)+" "+pad(tm.year);
    } else {
      return pad(tm.hour) + ":" + pad(tm.minute);
    }
  };
});
