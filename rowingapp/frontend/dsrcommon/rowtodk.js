angular.module('row.rowtodk', []).filter('rowtodk', function () {
  return function (rw) {
    if (!rw) return("");
    if (rw==1) return ("mod porten");
    if (rw==2) return ("inderst");
    return (rw);
  };
});
