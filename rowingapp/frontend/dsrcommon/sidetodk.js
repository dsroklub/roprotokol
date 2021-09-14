var side2dk = {
  'left':'venstre',
  'right':'højre',
  'center':'midtfor'
}

angular.module('rowApp.utilities.sidetodk', []).filter('sidetodk', function () {
  return function (sd) {
    var r=side2dk[sd];
    return (r?r:sd);
  };
});
